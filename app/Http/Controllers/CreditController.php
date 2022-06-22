<?php
namespace App\Http\Controllers;

use App\Role;
use App\ShopCreditHistory;
use App\ShopVoucher;
use App\ShopVouchersHistory;
use App\User;
use App\ShopCreditDedipassHistory;
use App\ShopCreditHipayHistory;
use App\ShopCreditPaypalHistory;
use App\ShopCreditPaysafecardHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CreditController extends Controller
{
    private $request;

    private $offers = [
      'PAYPAL' => [
          1500 => 19.99,
          2500 => 29.99,
          4800 => 39.99,
          6800 => 49.99,
          8800 => 59.99,
          13750 => 79.99
      ]
    ];

    public function add(Request $request)
    {
        return view('shop.credit-add', ['offers' => $this->offers]);
    }

    public function paymentCancel()
    {
        return redirect('/shop/credit/add')->with('flash.error', __('shop.credit.add.cancel'));
    }

    public function paymentSuccess()
    {
        return redirect('/shop/credit/add');
    }

    public function paymentError()
    {
        return redirect('/shop/credit/add')->with('flash.error', __('shop.credit.add.error'));
    }

    public function paypalNotification(Request $request)
    {
        $this->request = $request;
        // Check request
        $ipn = resolve('\Fahim\PaypalIPN\PaypalIPNListener');
        if (!$ipn->processIpn()) {
            Log::error($ipn->getTextReport());
            abort(403);
        }

        // Find user
        $user = User::where('id', $request->input('custom'))->firstOrFail();
        if (!$user->can('shop-credit-add') && strtoupper($request->input('payment_status')) !== 'CANCELED_REVERSAL')
            abort(403);
        // Check currency
        if ($request->input('mc_currency') !== 'USD')
            abort(403);
        // Check receiver
        if ($request->input('receiver_email') !== env('PAYPAL_EMAIL'))
            abort(403);

        // Try to find transaction
        $id_key = (strtoupper($request->input('payment_status')) === 'COMPLETED') ? 'txn_id' : 'parent_txn_id';
        $transaction = ShopCreditPaypalHistory::where('payment_id', $request->input($id_key))->first();

        // Handle types
        switch (strtoupper($request->input('payment_status'))) {
            case 'COMPLETED':
                // Already handled
                if (is_object($transaction) && $transaction->status === 'COMPLETED')
                    abort(403);
                // Find offer
                $amount = floatval($request->input('mc_gross'));
                if (!in_array($amount, $this->offers['PAYPAL']))
                    abort(404);

                // Add transaction
                $transaction = new ShopCreditPaypalHistory();
                $transaction->payment_amount = $request->input('mc_gross');
                $transaction->payment_tax = $request->input('mc_fee');
                $transaction->payment_id = $request->input('txn_id');
                $transaction->buyer_email = $request->input('payer_email');
                $transaction->payment_date = date('Y-m-d H:i:s', strtotime($request->input('payment_date')));
                $transaction->status = 'COMPLETED';
                $transaction->save();

                $this->save(
                    $user,
                    array_search($amount, $this->offers['PAYPAL']),
                    $amount,
                    'PAYPAL',
                    $transaction
                );
                break;
            case 'REVERSED':
            case 'REFUNDED':
                if (!is_object($transaction))
                    abort(404);

                // Edit rank on website
                $user->attachRole(Role::where('name', 'restricted')->first());
                $user->detachRole(Role::where('name', 'user')->first());

                // Edit transaction
                $transaction->status = strtoupper($request->input('payment_status'));
                $transaction->case_date = date('Y-m-d H:i:s');
                $transaction->save();

                break;
            case 'CANCELED_REVERSAL':
                if (!is_object($transaction))
                    abort(404);
                
                // Edit rank on website
                $user->attachRole(Role::where('name', 'user')->first());
                $user->detachRole(Role::where('name', 'restricted')->first());

                // Edit transaction
                $transaction->status = 'CANCELED_REVERSAL';
                $transaction->save();
                break;
            default:
                abort(404);
                break;
        }
    }

    public function voucher(Request $request)
    {
        if (!$request->has('code'))
            return response()->json([
                'status' => false,
                'error' => __('form.error.fields')
            ]);
        $voucher = ShopVoucher::where('code', $request->input('code'))->doesntHave('history')->first();
        if (!$voucher)
            return response()->json([
                'status' => false,
                'error' => __('shop.credit.add.error.voucher')
            ]);

        $history = new ShopVouchersHistory();
        $history->voucher_id = $voucher->id;
        $history->user_id = Auth::user()->id;
        $history->save();

        $this->save(Auth::user(), $voucher->money, 0, 'VOUCHER', $history);

        // Send notification to server
        $server = resolve('\Server');
        $server->sendCommand(strtr(env('DISCORD_SRV_BROADCAST'), ['{PLAYER}' => Auth::user()->username, '{MONEY}' => $voucher->money]))->get();

        return response()->json([
            'status' => true,
            'success' => __('shop.credit.add.success.voucher', ['money' => $voucher->money])
        ]);
    }

    private function save($user, $money, $amount, $type, $transaction)
    {
        // Save into history
        $history = new ShopCreditHistory();
        $history->transaction_type = $type;
        $history->user_id = $user->id;
        $history->money = $money;
        $history->amount = $amount;
        $history->transaction_id = $transaction->id;
        $history->save();

        // Add history to transaction
        $transaction->history_id = $history->id;
        $transaction->save();

        // Add money to user
        $currentUser = User::find($user->id);
        $currentUser->money = ($currentUser->money + floatval($money));
        $currentUser->save();

        // Notify
        $notification = new \App\Notification();
        $notification->user_id = $user->id;
        $notification->type = 'success';
        $notification->key = 'shop.credit.add.success';
        $notification->vars = ['money' => $money];
        $notification->auto_seen = 1;
        $notification->save();
    }

}