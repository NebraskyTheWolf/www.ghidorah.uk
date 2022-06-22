<?php
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
  return view('pages.home');
});
Route::get('/faq', function () {
  return view('pages.faq', ['questions' => \App\Question::get()]);
});
Route::get('/join', function () {
  return view('pages.join');
});

/*
===========
  USERS
===========
*/
Route::get('/login', function () {
  return view('user.login');
})->name('login');
Route::post('/login', 'UserController@login');
Route::get('/logged', function () {
  return response()->json([
    'logged' => Auth::check()
  ]);
});
Route::post('/login/two-factor-auth', 'UserController@validLogin');
Route::get('/logout', 'UserController@logout');

Route::get('/signup', function() {
  return view('user.signup');
});
Route::post('/signup', 'UserController@signup');
Route::get('/user/email/confirm/{token}', 'UserController@confirmEmail')->where('token', '[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}');
Route::get('/user/email/send', 'UserController@sendConfirmationMail')->middleware('auth')->middleware('permission:user-send-confirmation-email');

Route::get('/user', 'UserController@profile')->middleware('auth');
Route::get('/user/notification/seen/{id}', 'UserController@setNotificationAsSeen')->middleware('auth');

Route::post('/user/password/forgot', 'UserController@forgotPassword');
Route::get('/user/password/reset/{token}', function (\Illuminate\Http\Request $request) {
  // Find token
  $token = \App\UsersToken::where('token', $request->token)->where('type', 'PASSWORD')->where('used_ip', null)->where('created_at', '>', date('Y-m-d H:i:s', strtotime('-24 hours')))->firstOrFail();
  return view('user.password_reset');
})->where('token', '[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}');
Route::post('/user/password/reset/{token}', 'UserController@resetPassword')->where('token', '[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}');
Route::post('/user/password', 'UserController@editPassword')->middleware('auth')->middleware('permission:user-edit-password');

Route::post('/user/email', 'UserController@requestEditEmail')->middleware('auth')->middleware('permission:user-request-edit-email');
Route::post('/user/username', 'UserController@editUsername')->middleware('auth')->middleware('permission:user-edit-username');
Route::put('/user/money', 'UserController@transferMoney')->middleware('auth')->middleware('permission:user-transfer-money');
Route::post('/user/skin', 'UserController@uploadSkin')->middleware('auth')->middleware('permission:user-upload-skin');
Route::post('/user/cape', 'UserController@uploadCape')->middleware('auth')->middleware('permission:user-upload-cape');
Route::get('/user/two-factor-auth/enable', 'UserController@enableTwoFactorAuth')->middleware('auth')->middleware('permission:user-enable-two-factor-auth');
Route::post('/user/two-factor-auth/enable', 'UserController@validEnableTwoFactorAuth')->middleware('auth')->middleware('permission:user-enable-two-factor-auth');
Route::get('/user/two-factor-auth/disable', 'UserController@disableTwoFactorAuth')->middleware('auth')->middleware('permission:user-disable-two-factor-auth');

Route::get('/user/obsiguard/enable', 'ObsiguardController@enable')->middleware('auth')->middleware('permission:user-enable-obsiguard');
Route::post('/user/obsiguard/security/valid', 'ObsiguardController@validSecurityCode')->middleware('auth');
Route::get('/user/obsiguard/disable', 'ObsiguardController@disable')->middleware('auth')->middleware('permission:user-disable-obsiguard')->middleware('obsiguard');
Route::post('/user/obsiguard/ip', 'ObsiguardController@addIP')->middleware('auth')->middleware('permission:user-add-ip-obsiguard')->middleware('obsiguard');
Route::delete('/user/obsiguard/ip/{id}', 'ObsiguardController@removeIP')->middleware('auth')->middleware('permission:user-remove-ip-obsiguard')->middleware('obsiguard')->where('id', '([0-9])+');
Route::get('/user/obsiguard/ip/dynamic/enable', 'ObsiguardController@enableDynamicIP')->middleware('auth')->middleware('permission:user-enable-dynamic-ip-obsiguard');
Route::get('/user/obsiguard/ip/dynamic/disable', 'ObsiguardController@disableDynamicIP')->middleware('auth')->middleware('permission:user-disable-dynamic-ip-obsiguard');

Route::get('/user/socials/google/link', 'GoogleController@auth')->middleware('auth')->middleware('permission:user-link-google-account');
Route::get('/user/socials/youtube/videos', 'GoogleController@viewYoutubeVideos')->middleware('auth')->middleware('permission:user-youtube-view-own-videos');
Route::get('/user/socials/youtube/videos/{id}/remuneration', 'GoogleController@getYoutubeVideoRemuneration')->middleware('auth')->middleware('permission:user-youtube-get-remuneration')->where('id', '([0-9])+');

Route::get('/user/socials/twitter/link', 'TwitterController@auth')->middleware('auth')->middleware('permission:user-link-twitter-account');
Route::get('/user/socials/twitter/link/success', 'TwitterController@success')->middleware('auth')->middleware('permission:user-link-twitter-account');
Route::post('/user/socials/twitter/link/callback', 'TwitterController@callback');

Route::get('/user/server/logged/{user}', 'UserController@isLogged');

// SERVERS

// MAIN PARTNERSHIP SHOWING
Route::get('/servers', 'ServerController@index');
Route::get('/servers/partner', 'ServerController@about');
Route::get('/servers/feedback', 'ServerController@feedback')->middleware('auth');

// SERVERS STATS
Route::get('/server/{guildid}', 'ServerController@stats');
Route::get('/server/{guildid}/leaderboards', 'ServerController@leaderboards');
Route::get('/server/{guildid}/rules', 'ServerController@rules');
Route::get('/server/{guildid}/members', 'ServerController@members');
Route::get('/server/{guildid}/manifest', 'ServerController@manifest')->middleware('auth');

// SERVER USER STATS
Route::get('/server/{guildid}/{userid}/profile', 'ServerController@userProfile');
Route::get('/server/{guildid}/{userid}/sanctions', 'ServerController@userSanctions')->middleware('auth');
Route::get('/server/{guildid}/{userid}/manifest', 'ServerController@userManifest')->middleware('auth');

/*
===========
  VERIFY
===========
*/

Route::get('/verify', 'VoteController@index');
Route::get('/verify/position', 'VoteController@getWaitingQueue');

Route::post('/verify/step/one', 'VoteController@stepOne');
Route::post('/verify/step/two', 'VoteController@stepTwo');
Route::post('/verify/step/three', 'VoteController@stepThree');
Route::post('/verify/step/four', 'VoteController@stepFour');

/*
===========
  STATS
===========
*/
Route::get('/stats', 'StatsController@index');
Route::get('/stats/users/search', function (Request $request) {
  return response()->json([
    'status' => true,
    'users' => array_map(function ($user) {
      return [
        'username' => $user['username'],
        'img' => 'https://minotar.net/avatar/' . $user['username'] . '/32',
        'url' => url('/stats/' . $user['discord_id'])
      ];
    }, \App\User::where('username', 'LIKE', '%' . $_GET['q'] . '%')->get()->toArray())
  ]);
});
Route::get('/stats/users/count', 'StatsController@usersCount');
Route::get('/stats/users/graph', 'StatsController@usersGraph');
Route::get('/stats/users/graph/peak', 'StatsController@usersPeakGraph');
Route::get('/stats/users/graph/register', 'StatsController@usersRegisterGraph');
Route::get('/stats/users/graph/visits', 'StatsController@usersVisitsGraph');
Route::get('/stats/fights/count', 'StatsController@fightsCount');
Route::get('/stats/users/count/version', 'StatsController@usersCountThisVersion');
Route::get('/stats/server/count', 'StatsController@serverCount');
Route::get('/stats/server/max', 'StatsController@serverMax');
Route::get('/stats/visits/count', 'StatsController@visitsCount');

/*
===========
  WIKI
===========
*/
Route::get('/wiki', function () {
  return view('wiki.index', ['categories' => \App\WikiCategory::get()]);
});
Route::get('/wiki/{article}', function (\App\WikiArticle $article) {
  if (!$article->displayed && !Entrust::can('wiki-see-not-displayed-article'))
    return abort(403);
  return view('wiki.article', ['article' => $article]);
});

/*
===========
  SHOP
===========
*/
Route::get('/shop', 'ShopController@index')->middleware('auth');
Route::get('/shop/rank/{rankslug}', 'ShopController@index')->middleware('auth');
Route::get('/shop/item/{itemid}', 'ShopController@index')->middleware('auth');
Route::get('/shop/category/{categoryid}', 'ShopController@index')->middleware('auth');
Route::post('/shop/buy', 'ShopController@buy')->middleware('auth')->middleware('permission:shop-buy');

Route::get('/shop/credit/add', 'CreditController@add')->middleware('auth')->middleware('permission:shop-credit-add');
Route::get('/shop/credit/add/success', 'CreditController@paymentSuccess')->middleware('auth')->middleware('permission:shop-credit-add');
Route::get('/shop/credit/add/error', 'CreditController@paymentError')->middleware('auth')->middleware('permission:shop-credit-add');
Route::get('/shop/credit/add/cancel', 'CreditController@paymentCancel')->middleware('auth')->middleware('permission:shop-credit-add');
Route::get('/shop/credit/add/paysafecard/success', 'CreditController@paysafecardSuccess')->middleware('auth')->middleware('permission:shop-credit-add');

Route::post('/shop/credit/add/paypal/notification', 'CreditController@paypalNotification');
Route::post('/shop/credit/add/dedipass/notification', 'CreditController@dedipassNotification')->middleware('auth')->middleware('permission:shop-credit-add');
Route::post('/shop/credit/add/hipay/notification', 'CreditController@hipayNotification');
Route::post('/shop/credit/add/paysafecard/notification', 'CreditController@paysafecardNotification');
Route::post('/shop/credit/add/paysafecard/init', 'CreditController@paysafecardInit')->middleware('auth')->middleware('permission:shop-credit-add');

Route::post('/shop/credit/add/voucher', 'CreditController@voucher')->middleware('auth')->middleware('permission:shop-credit-add');

/*
===========
  SANCTIONS DEPRECATED
===========
*/
//Route::get('/sanctions', 'ContestController@index');
//Route::get('/sanctions/contest', 'ContestController@index');
//Route::post('/sanctions/contest', 'ContestController@add')->middleware('auth')->middleware('permission:sanction-contest');
//Route::get('/sanctions/contest/{id}', 'ContestController@view');
//Route::delete('/sanctions/contest/{id}', 'ContestController@close')->middleware('auth')->middleware('permission:sanction-contest-close');
//Route::put('/sanctions/contest/{id}', 'ContestController@edit')->middleware('auth')->middleware('permission:sanction-contest-edit');
//Route::post('/sanctions/contest/{id}/comment', 'ContestController@addComment')->middleware('auth');

/*
===========
  MOVIE
===========
*/

Route::group(['namespace' => 'Rooms', 'prefix' => 'rooms', 'middleware' => ['auth']], function () {
  Route::get('/', 'DashboardController@index'); // CALENDAR // ANNOUNCEMENTS
  
  Route::get('/rooms', 'RoomController@view');
  Route::get('/room/{id}', 'RoomController@view');

  Route::get('/profile', 'ProfileController@index');
  Route::get('/profile/friends', 'ProfileController@friends');

  Route::get('/about', 'AboutController@index');
});

// ADMIN MOVIE PANEL
Route::group(['namespace' => 'Movie', 'prefix' => 'movie', 'middleware' => ['permission:view-movie-dashboard']], function () {
    Route::get('/', 'DashboardController@index');

    Route::get('/room/{id}/calendar', 'CalendarController@index');
    Route::post('/room/{id}/calendar', 'CalendarController@update');
    Route::delete('/room/{id}/calendar/{key}', 'CalendarController@delete');

    Route::get('/announcements', 'AnnouncementsController@index');
    Route::post('/announcements', 'AnnouncementsController@index');
    Route::get('/announcements/{id}', 'AnnouncementsController@index');
    Route::delete('/announcements/{id}', 'AnnouncementsController@index');

    Route::get('/rooms', 'RoomsController@index');
    Route::get('/room/{id}', 'RoomsController@view');

    Route::get('/room/{id}/moderators', 'RoomsController@getModerator');
    Route::get('/room/{id}/settings', 'RoomsController@getSettings');
    Route::post('/room/{id}/settings/update', 'RoomsController@updateSettings');
    Route::get('/room/{id}/members', 'RoomsController@getMembers');
    Route::get('/room/{id}/manifest', 'RoomsController@getManifest');

    Route::delete('/room/delete/{id}/', 'RoomsController@delete');
    Route::post('/room/update/{id}', 'RoomsController@update');

    Route::get('/moderator/room/{id}', 'ModeratorController@index');
    Route::post('/moderator/room/{id}/{modid}', 'ModeratorController@update');
    Route::delete('/moderator/room/{id}/{modid}', 'ModeratorController@delete');

    Route::get('/status', 'StatusController@index');
    Route::get('/maintenance', 'StatusController@maintenance');
});

/*
===========
  ADMIN
===========
*/

Route::group(['namespace' => 'Admin', 'prefix' => 'admin', 'middleware' => ['permission:view-admin-dashboard']], function() {
    // DASHBOARD
    Route::get('/', 'DashboardController@index');

    // Users
    Route::get('/users', 'UserController@index');
    Route::get('/users/username/history', 'UserController@usernameHistory');
    Route::get('/users/find', 'UserController@find');
    Route::get('/users/edit/{id}', 'UserController@edit')->where('id', '[0-9]*');
    Route::get('/users/edit/{username}', 'UserController@edit')->where('username', '[A-Za-z_\-0-9]*');
    Route::post('/users/edit/{id}', 'UserController@editData')->where('id', '[0-9]*');
    Route::post('/users/edit/{id}/obsiguard/delete/{ipId}', 'UserController@deleteObsiguardIP')->where('id', '[0-9]*')->where('ipId', '[0-9]*');
    Route::get('/users/transfers', 'UserController@transferHistory');
    Route::get('/users/transfers/data', 'UserController@transferHistoryData');
    Route::get('/emails/updates', 'UserController@emailsUpdate');
    Route::post('/emails/updates/{id}/valid', 'UserController@emailsUpdateValid')->where('id', '[0-9]*');
    Route::post('/emails/updates/{id}/invalid', 'UserController@emailsUpdateInvalid')->where('id', '[0-9]*');

    // Shop
    Route::get('/shop/items', 'ShopController@items');

    Route::get('/shop/item/delete/{id}', 'ShopController@deleteItem')->where('id', '[0-9]*');
    Route::get('/shop/item/edit/{id}', 'ShopController@editItem')->where('id', '[0-9]*');
    Route::get('/shop/item/add', 'ShopController@editItem');
    Route::post('/shop/item/edit/{id}', 'ShopController@editItemData')->where('id', '[0-9]*');
    Route::post('/shop/item/add', 'ShopController@editItemData');

    Route::get('/shop/category/edit/{id}', 'ShopController@editCategory')->where('id', '[0-9]*');
    Route::get('/shop/category/add', 'ShopController@editCategory');
    Route::post('/shop/category/edit/{id}', 'ShopController@editCategoryData')->where('id', '[0-9]*');
    Route::post('/shop/category/add', 'ShopController@editCategoryData');
    Route::get('/shop/category/delete/{id}', 'ShopController@deleteCategory')->where('id', '[0-9]*');

    Route::get('/shop/sale/edit/{id}', 'ShopController@editSale')->where('id', '[0-9]*');
    Route::get('/shop/sale/add', 'ShopController@editSale');
    Route::post('/shop/sale/edit/{id}', 'ShopController@editSaleData')->where('id', '[0-9]*');
    Route::post('/shop/sale/add', 'ShopController@editSaleData');
    Route::get('/shop/sale/delete/{id}', 'ShopController@deleteSale')->where('id', '[0-9]*');

    Route::get('/shop/voucher/edit/{id}', 'ShopController@editVoucher')->where('id', '[0-9]*')->middleware('permission:shop-admin-vouchers');
    Route::get('/shop/voucher/add', 'ShopController@editVoucher')->middleware('permission:shop-admin-vouchers');
    Route::post('/shop/voucher/edit/{id}', 'ShopController@editVoucherData')->where('id', '[0-9]*')->middleware('permission:shop-admin-vouchers');
    Route::post('/shop/voucher/add', 'ShopController@editVoucherData')->middleware('permission:shop-admin-vouchers');
    Route::get('/shop/voucher/delete/{id}', 'ShopController@deleteVoucher')->where('id', '[0-9]*')->middleware('permission:shop-admin-vouchers');

    Route::get('/shop/history', 'ShopController@history');
    Route::get('/shop/history/data/items', 'ShopController@historyDataItems');
    Route::get('/shop/history/data/credits', 'ShopController@historyDataCredits');
    Route::get('/shop/history/data/paypal', 'ShopController@historyDataPaypal');
    Route::get('/shop/history/data/dedipass', 'ShopController@historyDataDedipass');
    Route::get('/shop/history/data/hipay', 'ShopController@historyDataHipay');
    Route::get('/shop/history/data/paysafecard', 'ShopController@historyDataPaysafecard');

    // Stats
    Route::get('/stats/shop', 'StatsController@shop')->middleware('permission:view-admin-stats-shop');
    Route::get('/stats/shop/graph/purchases/credits', 'StatsController@graphPurchasesCredits');
    Route::get('/stats/shop/graph/purchases/credits/modes', 'StatsController@graphPurchasesCreditsModes');
    Route::get('/stats/shop/graph/purchases/items', 'StatsController@graphPurchasesItems');
    Route::get('/stats/shop/graph/purchases/items/total', 'StatsController@graphPurchasesItemsTotal');
    Route::get('/stats/shop/graph/transfers', 'StatsController@graphTransfers');
});
