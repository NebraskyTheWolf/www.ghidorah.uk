<?php
namespace App\Http\Controllers;

use function GuzzleHttp\Psr7\str;
use Illuminate\Support\Facades\Log;
use Mockery\Exception;
use Urb\XenforoBridge\XenforoBridge;
use Validator;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;
use App\User;
use App\UsersToken;

use App\Mail\UserSignup;
use App\Mail\UserForgotPassword;
use Illuminate\Support\Facades\Mail;

use Carbon\Carbon;

class UserController extends Controller
{
	public function __construct()
	{
		Carbon::setLocale(\Config::get('app.locale'));
		Carbon::setToStringFormat('d/m/Y à H:i:s');
	}

	public function login(Request $request)
	{
		// Check form
		if (!$request->has('username') || !$request->has('password'))
			return response()->json([
					'status' => false,
					'error' => __('form.error.fields')
			]);

		// Check if not temporaly disabled
		$lastRetry = \App\UsersLoginRetry::where('ip', $request->ip())->where('updated_at', '>', date('Y-m-d H:i:s', strtotime('-30 minutes')))->first();
		if (!empty($lastRetry) && $lastRetry->count >= 10) // Temporaly blocked
			return response()->json([
					'status' => false,
					'error' => __('user.login.error.blocked')
			]);

		// Check credentials
		$user = User::where('username', $request->input('username'))->first();
		if (empty($user)) // User not found
			return response()->json([
					'status' => false,
					'error' => __('user.login.error.notfound')
			]);
		if ($user->password !== User::hash($request->input('password'), $request->input('username'))) { // invalid password
			// save try
			$count = (empty($lastRetry)) ? 1 : ($lastRetry->count + 1);
			\App\UsersLoginRetry::updateOrCreate(
					['ip' => $request->ip()],
					['count' => $count]
					);
			// send response
			return response()->json([
					'status' => false,
					'error' => __('user.login.error.credentials')
			]);
		}

		// Check if two factor auth
		$twoFactorAuthSecret = \App\UsersTwoFactorAuthSecret::where('user_id', $user->id)->first();
		if (!empty($twoFactorAuthSecret) && $twoFactorAuthSecret->enabled) { // TwoFactor is enabled, store user_id & send to user
			session(['twoFactorAuth' => [
					'user_id' => $user->id,
					'remember_me' => $request->input('remember_me'),
					'user_pass' => $request->input('password')
			]]);
			return response()->json([
					'status' => true,
					'twoFactorAuth' => true,
					'success' => ''
			]);
		}

		// Success
		return $this->__loginSuccess($user->id, $request->input('remember_me'), $request->input('password'));
	}

	public function validLogin(Request $request)
	{
		// Check form
		if (!$request->has('code'))
			return response()->json([
					'status' => false,
					'error' => __('form.error.fields')
			]);
		// Check session
		if (!$request->session()->has('twoFactorAuth'))
			return abort(403);
		$userId = $request->session()->get('twoFactorAuth')['user_id'];
		$password = $request->session()->get('twoFactorAuth')['user_pass'];
		$rememberMe = $request->session()->get('twoFactorAuth')['remember_me'];

		// Get secret
		$twoFactorAuthSecret = \App\UsersTwoFactorAuthSecret::where('user_id', $userId)->first();
		if (empty($twoFactorAuthSecret) || !$twoFactorAuthSecret->enabled)
			return abort(403);

		// Valid secret
		$ga = new \PHPGangsta_GoogleAuthenticator();

		// check code
		$checkResult = $ga->verifyCode($twoFactorAuthSecret->secret, $request->input('code'), 2);    // 2 = 2*30sec clock tolerance
		if (!$checkResult)
			return response()->json([
					'status' => false,
					'error' => __('user.login.error.two_factor_auth')
			]);

		// Remove session
		$request->session()->forget('twoFactorAuth');

		return $this->__loginSuccess($userId, $rememberMe, $password);
	}

	private function __loginSuccess($userId, $rememberMe, $password)
	{
		// Log user
		Auth::loginUsingId($userId, ($rememberMe ? true : false));

		if (env('APP_FORUM_ENABLED', false))
        {
            try {
                resolve(\Urb\XenforoBridge\XenforoBridge::class)->login(Auth::user()->username, $password, $rememberMe);
            } catch (\Exception $e) {
                Log::warning($e->getMessage());
            }
        }

		return response()->json([
				'status' => true,
				'success' => __('user.login.success')
		]);
	}

	public function logout(Request $request)
	{
        if (env('APP_FORUM_ENABLED', false)) {
            try {
                resolve(\Urb\XenforoBridge\XenforoBridge::class)->logout(Auth::user()->username);
            } catch (\Exception $e) {
                Log::warning($e->getMessage());
            }
        }
		Auth::logout();
		return redirect('/');
	}

	public function preSignup(Request $request) {
		if (!$request->has('token'))
			return response()->json([
					'status' => false,
					'error' => __('form.error.fields')
			]);
		
		if (!Cache::has('user.' . $request->input('token') . 'cache')) {
            $file = file_get_contents('https://api.skf-studios.com:8443/user/by-token/' . $request->input('token'));
            $content = @json_decode($file, true);
            $max = @$content['activated'];
            if (!$max)
                return response()->json(['status' => false, 'error' => __('user.signup.invalid.token')]);

            // Store
            Cache::put('user.' . $request->input('token') . 'cache', $max, 15); // 15 minutes
        }

		return response()->json([
			'status' => true,
			'success' => __('user.presignup.success'),
			'redirect' => url('/signup')
		]);
	}

	public function signup(Request $request)
	{
		// Check form
		if (!$request->has('token') || !$request->has('username') || !$request->has('discordid') || !$request->has('password') || !$request->has('password_confirmation') || !$request->has('email'))
			return response()->json([
					'status' => false,
					'error' => __('form.error.fields')
			]);
		if (!$request->has('legal'))
			return response()->json([
					'status' => false,
					'error' => __('user.signup.error.legal')
			]);

		$file = file_get_contents('https://api.skf-studios.com:8443/user/verify/' . $request->input('discordid') . '/917714328327692338/fetch');
		$content = @json_decode($file, true);

		// Check verified user

		if (!$content['status'] && $content['verifiedId'] !== 'verified')
			return response()->json([
				'status' => false,
				'error' => 'You need to do /verify in the discord server.'
			]);
		
		// Check email
		if (Validator::make(['email' => $request->input('email')], ['email' => 'required|email'])->fails())
			return response()->json([
					'status' => false,
					'error' => __('user.signup.error.email')
			]);
		// Check passwords
		if ($request->input('password') !== $request->input('password_confirmation'))
			return response()->json([
					'status' => false,
					'error' => __('user.signup.error.passwords')
			]);

		// Check if username or email is already used
		$findUserWithUsernameOrEmail = User::where('username', $request->input('username'))->orWhere('email', $request->input('email'))->first();
		if (!empty($findUserWithUsernameOrEmail)) {
			if ($findUserWithUsernameOrEmail->username == $request->input('username'))
				return response()->json([
						'status' => false,
						'error' => __('user.signup.error.username.taken')
				]);
			else
				return response()->json([
						'status' => false,
						'error' => __('user.signup.error.email.taken')
				]);
		}

		// register user
		$user = new User();
		$user->uuid = \Uuid::generate();
		$user->username = $request->input('username');
		$user->link_token = $request->input('token');
		$user->discord_id = $request->input('discordid');
		$user->email = $request->input('email');
		$user->password = User::hash($request->input('password'), $request->input('username'));
		$user->ip = $request->ip();
        $user->save();
        $user->roles()->attach(env('APP_DEFAULT_ROLE', 1)); // attach default role

		$file = file_get_contents('https://api.skf-studios.com:8443/user/activate/' . $request->input('discordid'));
		$content = @json_decode($file, true);

		// Add to Xenforo
		if (env('APP_FORUM_ENABLED', false)) {
            try {
                resolve(\Urb\XenforoBridge\XenforoBridge::class)->register($request->input('username'), $request->input('password'), $request->input('email'));
                resolve(\Urb\XenforoBridge\XenforoBridge::class)->login($request->input('username'), $request->input('password'), false);
            } catch (\Exception $e) {
                Log::warning($e->getMessage());
            }
        }

		// generate confirmation token
		$token = UsersToken::generate('EMAIL', $user->id);
		$link = action('UserController@confirmEmail', ['token' => $token]);

		// send confirmation mail
		//Mail::to($user->email)->send(new UserSignup($user, $link));

		// log user
		Auth::loginUsingId($user->id, false);

		// success response
		return response()->json([
				'status' => true,
				'success' => __('user.signup.success'),
				'redirect' => url('/user')
		]);
	}

	public function confirmEmail(Request $request)
	{
		// Find token
		$token = UsersToken::where('token', $request->token)->where('type', 'EMAIL')->where('used_ip', null)->firstOrFail();

		// Set token as used
		$token->used_ip = $request->ip();
		$token->save();

		// Redirect with flash
		return redirect('/user')->with('flash.success', __('user.signup.email.confirmed'));
	}

	public function sendConfirmationMail(Request $request)
	{
		// Find token
		$token = UsersToken::where('user_id', Auth::user()->id)->where('type', 'EMAIL')->where('used_ip', null)->firstOrFail();
		$link = action('UserController@confirmEmail', ['token' => $token->token]);

		// send confirmation mail
		//Mail::to(Auth::user()->email)->send(new UserSignup(Auth::user(), $link));

		// Redirect with flash
		return redirect('/user')->with('flash.success', __('user.signup.email.confirmation.sended'));
	}

	public function profile(Request $request)
	{
		// VOTES
		$votesCount = \App\Vote::where('user_id', Auth::user()->id)->where('created_at', '>', 0)->count();
		$rewardsWaitedCount = \App\Vote::where('user_id', Auth::user()->id)->where('reward_getted', false)->count();

		// EMAIL CONFIRMED
		$findEmailToken = UsersToken::where('user_id', Auth::user()->id)->where('type', 'EMAIL')->where('used_ip', null)->first();
		$confirmedAccount = (empty($findEmailToken));

		// TWO FACTOR AUTH
		$findTwoFactorAuthSecret = \App\UsersTwoFactorAuthSecret::where('user_id', Auth::user()->id)->first();
		$twoFactorEnabled = ($findTwoFactorAuthSecret && $findTwoFactorAuthSecret->enabled);

		// OBSIGUARD
		$findObsiGuardIPs = \App\UsersObsiguardIP::where('user_id', Auth::user()->id)->get();
		$obsiguardDynamicIP = Auth::user()->obsiguard_dynamic;

		// NOTIFICATION
		$notifications = \App\Notification::getUnseen(Auth::user()->id);

		// WEBSITE LOGIN LOGS
		$websiteLoginLogs = \App\UsersConnectionLog::getWebsiteLogs(Auth::user());

		// LAUNCHER LAUNCHER LOGS
		$launcherLoginLogs = \App\UsersConnectionLog::getLauncherLogs(Auth::user());

		// SOCIALS
		$youtube = \App\UsersYoutubeChannel::where('user_id', Auth::user()->id)->first();
		$twitter = \App\UsersTwitterAccount::where('user_id', Auth::user()->id)->first();

		$file = file_get_contents('https://api.skf-studios.com:8443/user/by-token/' . Auth::user()->link_token);
		$discordUser = @json_decode($file, true);

		// SPENDINGS LOGS
		$itemsPurchaseLogs = \App\ShopItemsPurchaseHistory::where('user_id', Auth::user()->id)->orderBy('id', 'DESC')->limit(8)->with('item')->get();
		$moneyTransferLogs = \App\UsersTransferMoneyHistory::where('user_id', Auth::user()->id)->orderBy('id', 'DESC')->limit(8)->with('receiver')->get();
		$spendings = array_merge(
				array_map(function ($data) {
					$data['type'] = 'item';
					return $data;
					}, $itemsPurchaseLogs->toArray()),
				array_map(function ($data) {
					$data['type'] = 'money';
					return $data;
					}, $moneyTransferLogs->toArray())
				);
		// sort
		usort($spendings, function ($a, $b) {
				return strtotime($b['created_at']) - strtotime($a['created_at']);
				});

		// CAPE
		$cape = \App\UsersEditCapeAbility::where('user_id', Auth::user()->id)->count();

		// RENDER
		return view('user.profile', compact('votesCount', 'rewardsWaitedCount', 'confirmedAccount', 'twoFactorEnabled', 'findObsiGuardIPs', 'obsiguardDynamicIP', 'notifications', 'websiteLoginLogs', 'launcherLoginLogs', 'youtube', 'twitter', 'discordUser', 'spendings', 'cape'));
	}

	public function setNotificationAsSeen(Request $request)
    {
        $notification = \App\Notification::where('user_id', Auth::user()->id)->where('id', $request->id)->firstOrFail();
        $notification->delete();
        return redirect('/user');
    }

	public function forgotPassword(Request $request)
	{
		if (!$request->has('email'))
			return response()->json([
					'status' => false,
					'error' => __('form.error.fields')
			]);
		// find user
		$user = User::where('email', $request->input('email'))->first();
		if (!$user)
			return response()->json([
					'status' => false,
					'error' => __('user.password.forgot.user.notfound')
			]);

		// generate reset token
		$token = UsersToken::generate('PASSWORD', $user->id);
		$link = url('/user/password/reset/' . $token);

		// send confirmation mail
		Mail::to($user->email)->send(new UserForgotPassword($user, $link));

		// success
		return response()->json([
				'status' => true,
				'success' => __('user.password.forgot.success')
		]);
	}

	public function resetPassword(Request $request)
	{
		// Check form
		if (!$request->has('password') || !$request->has('password_confirmation'))
			return response()->json([
					'status' => false,
					'error' => __('form.error.fields')
			]);
		if ($request->input('password') !== $request->input('password_confirmation'))
			return response()->json([
					'status' => false,
					'error' => __('user.signup.error.passwords')
			]);

		// Find token
		$token = UsersToken::where('token', $request->token)->where('type', 'PASSWORD')->where('used_ip', null)->where('created_at', '>', date('Y-m-d H:i:s', strtotime('-24 hours')))->firstOrFail();

		// Set token as used
		$token->used_ip = $request->ip();
		$token->save();

		// edit password
		$user = User::where('id', $token->user_id)->firstOrFail();
		$user->password = User::hash($request->input('password'), $user->username);
		$user->save();

        // Add to Xenforo
        if (env('APP_FORUM_ENABLED', false)) {
            try {
                resolve(\Urb\XenforoBridge\XenforoBridge::class)->editUser($user->username, 'password', $request->input('password'));
                resolve(\Urb\XenforoBridge\XenforoBridge::class)->login($user->username, $request->input('password'), false);
            } catch (\Exception $e) {
                Log::warning($e->getMessage());
            }
        }

		// login
		Auth::loginUsingId($user->id, false);

		// Redirect with flash
		return response()->json([
				'status' => true,
				'success' => __('user.password.reset.success'),
				'redirect' => url('/user')
		]);
	}

	public function editPassword(Request $request)
	{
		// Check form
		if (!$request->has('password') || !$request->has('password_confirmation'))
			return response()->json([
					'status' => false,
					'error' => __('form.error.fields')
			]);
		if ($request->input('password') !== $request->input('password_confirmation'))
			return response()->json([
					'status' => false,
					'error' => __('user.signup.error.passwords')
			]);

		// edit password
		$user = User::where('id', Auth::user()->id)->firstOrFail();
		$user->password = User::hash($request->input('password'), $user->username);
		$user->save();

        // Add to Xenforo
        if (env('APP_FORUM_ENABLED', false))
        {
            try {
                resolve(\Urb\XenforoBridge\XenforoBridge::class)->editUser($user->username, 'password', $request->input('password'));
            } catch (\Exception $e) {
                Log::warning($e->getMessage());
            }
        }

            // success
		return response()->json([
				'status' => true,
				'success' => __('user.password.edit.success')
		]);
	}

	public function requestEditEmail(Request $request)
	{
		// Check form
		if (!$request->has('email') || !$request->has('reason'))
			return response()->json([
					'status' => false,
					'error' => __('form.error.fields')
			]);
		// Check email
		if (Validator::make(['email' => $request->input('email')], ['email' => 'required|email'])->fails())
			return response()->json([
					'status' => false,
					'error' => __('user.signup.error.email')
			]);
		// Check if username or email is already used
		$findUserWithEmail = User::where('email', $request->input('email'))->first();
		if (!empty($findUserWithEmail))
			return response()->json([
					'status' => false,
					'error' => __('user.signup.error.email.taken')
			]);

		// check if already requested
		$emailRequest = \App\UsersEmailEditRequest::where('user_id', Auth::user()->id)->first();
		if ($emailRequest > 0)
			return response()->json([
					'status' => false,
					'error' => __('user.email.edit.request.already')
			]);

		// add request
		$emailRequest = new \App\UsersEmailEditRequest();
		$emailRequest->user_id = Auth::user()->id;
		$emailRequest->email = $request->input('email');
		$emailRequest->reason = $request->input('reason');
		$emailRequest->ip = $request->ip();
		$emailRequest->save();

		// success
		return response()->json([
				'status' => true,
				'success' => __('user.email.edit.request.success')
		]);
	}

	public function editUsername(Request $request)
	{
		// Check if purchased it
		$findAbility = \App\UsersEditUsernameAbility::where('user_id', Auth::user()->id)->where('history_id', NULL)->first();
		if ($findAbility <= 0)
			return response()->json([
					'status' => false,
					'error' => __('user.profile.edit.username.error.purchase')
			]);
		// Check if already edited 2 times
		$findEdits = \App\UsersEditUsernameAbility::where('user_id', Auth::user()->id)->whereNotNull('history_id')->orderBy('id', 'DESC')->get();
		if ($findEdits >= 2)
			return response()->json([
					'status' => false,
					'error' => __('user.profile.edit.username.error.two_times')
			]);
		// Check if edited last 2 weeks
		if (isset($findEdits[0]) && time() < $findEdits[0]->created_at->addWeeks(3)->timestamp)
			return response()->json([
					'status' => false,
					'error' => __('user.profile.edit.username.error.two_weeks')
			]);
		// Check form
		if (!$request->has('username') || !$request->has('password'))
			return response()->json([
					'status' => false,
					'error' => __('form.error.fields')
			]);
		// check password
		if (User::hash($request->input('password'), Auth::user()->username) !== Auth::user()->password)
			return response()->json([
					'status' => false,
					'error' => __('user.profile.edit.username.error.password')
			]);

		// check username
		if (!preg_match('`^([a-zA-Z0-9_]{2,16})$`', $request->input('username')))
			return response()->json([
					'status' => false,
					'error' => __('user.signup.error.username')
			]);
		// check if used
		$findUserWithUsername = User::where('username', $request->input('username'))->first();
		if (!empty($findUserWithUsername))
			return response()->json([
					'status' => false,
					'error' => __('user.signup.error.username.taken')
			]);

		// edit database
		$user = User::find(Auth::user()->id);
		$user->username = $request->input('username');
		$user->password = User::hash($request->input('password'), $request->input('username'));
		$user->save();

		// add log
		$log = new \App\UsersEditUsernameHistory();
		$log->user_id = Auth::user()->id;
		$log->old_username = Auth::user()->username;
		$log->new_username = $request->input('username');
		$log->ip = $request->ip();
		$log->save();

		// remove ability
		$findAbility->history_id = $log->id;
		$findAbility->save();

        if (env('APP_FORUM_ENABLED', false))
        {
            try {
                resolve(\Urb\XenforoBridge\XenforoBridge::class)->editUser($user->username, 'username', $request->input('username'));
            } catch (\Exception $e) {
                Log::warning($e->getMessage());
            }
        }

		// success message
		return response()->json([
				'status' => true,
				'success' => __('user.profile.edit.username.success')
		]);
	}

	public function transferMoney(Request $request)
	{
		// Check form
		if (!$request->has('amount') || !$request->has('to'))
			return response()->json([
					'status' => false,
					'error' => __('form.error.fields')
			]);
		if (Validator::make(['amount' => $request->input('amount')], ['amount' => 'required|numeric'])->fails())
			return response()->json([
					'status' => false,
					'error' => __('user.profile.transfer.money.error.amount')
			]);

		// check times limit
		$transfersToday = \App\UsersTransferMoneyHistory::where('user_id', Auth::user()->id)->where('created_at', 'LIKE', date('Y-m-d').'%')->get();
		if ($transfersToday >= 3)
			return response()->json([
					'status' => false,
					'error' => __('user.profile.transfer.money.error.limit.times')
			]);

		// check amount limit
		$sumToday = 0;
		if ($transfersToday > 0) {
			foreach ($transfersToday as $transfer) {
				$sumToday += $transfer->amount;
			}
			if ($sumToday + floatval($request->input('amount')) > 2250)
				return response()->json([
						'status' => false,
						'error' => __('user.profile.transfer.money.error.limit.day')
				]);
		}

		// check username
		if (strtolower(Auth::user()->username) == strtolower($request->input('to')))
			return response()->json([
					'status' => false,
					'error' => __('user.profile.transfer.money.error.himself')
			]);
		$recipientUser = User::where('username', $request->input('to'))->first();
		if ($recipientUser <= 0)
			return response()->json([
					'status' => false,
					'error' => __('user.profile.transfer.money.error.unknown_user')
			]);

		// check amount
		if (Auth::user()->money <= 0)
			return response()->json([
					'status' => false,
					'error' => __('user.profile.transfer.money.error.no_enough')
			]);
		$currentUser = User::find(Auth::user()->id);
		if (($currentUser->money - floatval($request->input('amount'))) < 0)
			return response()->json([
					'status' => false,
					'error' => __('user.profile.transfer.money.error.no_enough')
			]);
		if (floatval($request->input('amount')) <= 0)
			return response()->json([
					'status' => false,
					'error' => __('user.profile.transfer.money.error.negative')
			]);

		// check if ban
		$api = resolve('\ApiObsifight');
		$result = $api->get('/user/' . Auth::user()->username . '/sanctions/banned');
		if ($result->status && $result->success && $result->body && is_object($result->body) && $result->body->banned)
			return response()->json([
					'status' => false,
					'error' => __('user.profile.transfer.money.error.limit.ban')
			]);

		// edit users
		$currentUser->money = ($currentUser->money - floatval($request->input('amount')));
		$currentUser->save();
		$recipientUser->money = ($recipientUser->money + floatval($request->input('amount')));
		$recipientUser->save();

		// add to history
		$log = new \App\UsersTransferMoneyHistory();
		$log->user_id = $currentUser->id;
		$log->amount = $request->input('amount');
		$log->to = $recipientUser->id;
		$log->ip = $request->ip();
		$log->save();

		// success message
		return response()->json([
				'status' => true,
				'success' => __('user.profile.transfer.money.success', ['money' => $request->input('amount'), 'username' => $recipientUser->username]),
				'money' => $currentUser->money
		]);
	}

	public function uploadSkin(Request $request)
	{
		// check vote
		$votesCount = \App\Vote::where('user_id', Auth::user()->id)->where('created_at', '>', date('Y-m-00 00:00:00'))->count();
		if ($votesCount < 3)
			return abort(403);

		// check request
		if (!$request->hasFile('image'))
			return response()->json([
					'status' => false,
					'error' => __('user.profile.upload.error.no_file')
			]);
		if (Validator::make($request->all(), ['image' => 'required|max:' . env('SKINS_UPLOAD_MAX_SIXE') . '|mimes:png|dimensions:max_width=' . env('SKINS_UPLOAD_MAX_WIDTH') . ',max_height=' . env('SKINS_UPLOAD_MAX_HEIGHT')])->fails())
			return response()->json([
					'status' => false,
					'error' => __('user.profile.upload.error.file.type')
			]);

		// upload
		$path = $request->file('image')->storeAs(
				env('SKINS_UPLOAD_FTP_PATH'), str_replace('{PLAYER}', Auth::user()->username, env('SKINS_UPLOAD_FTP_FILENAME')), env('SKINS_UPLOAD_CONFIG')
				);

		// success
		return response()->json([
				'status' => true,
				'success' => __('user.profile.upload.success')
		]);
	}

	public function uploadCape(Request $request)
	{
		// can
		if (!\App\UsersEditCapeAbility::where('user_id', Auth::user()->id)->count())
			return abort(403);
		// check vote
		$votesCount = \App\Vote::where('user_id', Auth::user()->id)->where('created_at', '>', date('Y-m-00 00:00:00'))->count();
		if ($votesCount < 3)
			return abort(403);

		// check request
		if (!$request->hasFile('image'))
			return response()->json([
					'status' => false,
					'error' => __('user.profile.upload.error.no_file')
			]);
		if (Validator::make($request->all(), ['image' => 'required|max:' . env('SKINS_UPLOAD_MAX_SIXE') . '|mimes:png|dimensions:max_width=' . env('SKINS_UPLOAD_MAX_WIDTH') . ',max_height=' . env('SKINS_UPLOAD_MAX_HEIGHT')])->fails())
			return response()->json([
					'status' => false,
					'error' => __('user.profile.upload.error.file.type')
			]);

		// upload
		$path = $request->file('image')->storeAs(
				env('CAPES_UPLOAD_FTP_PATH'), str_replace('{PLAYER}', Auth::user()->username, env('CAPES_UPLOAD_FTP_FILENAME')), env('SKINS_UPLOAD_CONFIG')
				);

		// success
		return response()->json([
				'status' => true,
				'success' => __('user.profile.upload.success')
		]);
	}

	public function enableTwoFactorAuth(Request $request)
	{
		// Check if already enabled
		$secret = \App\UsersTwoFactorAuthSecret::where('user_id', Auth::user()->id)->where('enabled', true)->first();
		if ($secret > 0)
			return redirect('/user')->with('flash.error', __('user.profile.two_factor_auth.enable.error.already'));

		// Generate secret
		if (!class_exists('PHPGangsta_GoogleAuthenticator'))
			require base_path('vendor/PHPGangsta/GoogleAuthenticator.php');
		$ga = new \PHPGangsta_GoogleAuthenticator();

		$secret = $ga->createSecret();
		$qrCodeUrl = $ga->getQRCodeGoogleUrl(Auth::user()->username, $secret, env('APP_NAME'));

		// render
		$request->session()->put('user.twoFactorAuth.secret', $secret);
		return view('user.two_factor_auth', compact('secret', 'qrCodeUrl'));
	}

	public function validEnableTwoFactorAuth(Request $request)
	{
		// Check session
		if (!$request->session()->has('user.twoFactorAuth.secret'))
			return abort(404);
		$secretKey = $request->session()->get('user.twoFactorAuth.secret');
		// Check form
		if (!$request->has('code'))
			return response()->json([
					'status' => false,
					'error' => __('form.error.fields')
			]);
		// Check if already enabled
		$secret = \App\UsersTwoFactorAuthSecret::where('user_id', Auth::user()->id)->first();
		if ($secret > 0 && $secret->enabled)
			return response()->json([
					'status' => false,
					'error' => __('user.profile.two_factor_auth.enable.error.already')
			]);
		// check code
		if (!class_exists('PHPGangsta_GoogleAuthenticator'))
			require base_path('vendor/PHPGangsta/GoogleAuthenticator.php');
		$ga = new \PHPGangsta_GoogleAuthenticator();
		if (!$ga->verifyCode($secretKey, $request->input('code'), 2))   // 2 = 2*30sec clock tolerance)
			return response()->json([
					'status' => false,
					'error' => __('user.profile.two_factor_auth.enable.error.code')
			]);
		// remove session
		$request->session()->forget('user.twoFactorAuth.secret');

		// save secret
		if ($secret <= 0)
			$secret = new \App\UsersTwoFactorAuthSecret();
		$secret->user_id = Auth::user()->id;
		$secret->enabled = true;
		$secret->secret = $secretKey;
		$secret->save();

		// success
		$request->session()->put('flash.success', __('user.profile.two_factor_auth.enable.success'));
		return response()->json([
				'status' => true,
				'success' => __('user.profile.two_factor_auth.enable.success'),
				'redirect' => url('/user')
		]);
	}

	public function disableTwoFactorAuth(Request $request)
	{
		$secret = \App\UsersTwoFactorAuthSecret::where('user_id', Auth::user()->id)->first();
		if ($secret > 0) {
			$secret->enabled = false;
			$secret->save();
		}
		return redirect('/user')->with('flash.success', __('user.profile.two_factor_auth.disable.success'));
	}

	public function isLogged(Request $request)
	{
		return response()->json([
				'status' => true,
				'logged' => resolve('\Server')->isConnected($request->user)->get()['isConnected']
		]);
	}

}
