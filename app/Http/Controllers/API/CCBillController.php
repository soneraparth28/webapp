<?php /** @noinspection ALL */

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\Functions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\AdminSettings;
use App\Models\Subscriptions;
use App\Models\Notifications;
use App\Models\PaymentGateways;
use App\Models\Transactions;
use App\Models\Conversations;
use App\Models\Deposits;
use App\Models\Messages;
use App\Models\Updates;
use App\Models\User;
use Carbon\Carbon;
use App\Helper;
use Mail;

class CCBillController extends Controller
{
    use Functions;

    public function approved(Request $request)
    {
        if (isset($request->type) && $request->type == 'subscription') {
            $creator = User::findOrFail($request->creator);
            session()->put('subscription_success', trans('general.subscription_success'));
            $response = [
                'success' => true,
                'data' => [
                    'redirect_url' => $creator->username,
                ],
                'message' => 'Redirect Url.'
            ];
            return response()->json($response , 200);
//            return redirect($creator->username);

        } elseif (isset($request->type) && $request->type == 'tip') {
            $creator = User::findOrFail($request->creator);
            if (isset($request->isMessage)) {
                $response = [
                    'success' => true,
                    'data' => [
                        'redirect_url' => 'messages/'.$creator->id,
                    ],
                    'message' => 'Redirect Url.'
                ];
                return response()->json($response , 200);
//                return redirect('messages/'.$creator->id);
            } else {
                session()->put('subscription_success', trans('general.tip_sent_success'));
                $response = [
                    'success' => true,
                    'data' => [
                        'redirect_url' => $creator->username,
                    ],
                    'message' => 'Redirect Url.'
                ];
                return response()->json($response , 200);
//                return redirect($creator->username);
            }
        } elseif (isset($request->type) && $request->type == 'wallet') {
            $response = [
                'success' => true,
                'data' => [
                    'redirect_url' => 'my/wallet',
                ],
                'message' => 'Redirect Url.'
            ];
            return response()->json($response , 200);
//            return redirect('my/wallet');
        }
        elseif (isset($request->type) && $request->type == 'ppv') {
            if (isset($request->isMessage)) {
                // Check if it is a Message or Post
                $media = Messages::find($request->media);
                $response = [
                    'success' => true,
                    'data' => [
                        'redirect_url' => 'messages/'.$media->from_user_id,
                    ],
                    'message' => 'Redirect Url.'
                ];
                return response()->json($response , 200);
//                return redirect('messages/'.$media->from_user_id);
            } else {
                $response = [
                    'success' => true,
                    'data' => [
                        'redirect_url' => 'my/purchases',
                    ],
                    'message' => 'Redirect Url.'
                ];
                return response()->json($response , 200);
//                return redirect('my/purchases');
            }
        } else {
            $response = [
                'success' => true,
                'data' => [
                    'redirect_url' => '/',
                ],
                'message' => 'Redirect Url.'
            ];
            return response()->json($response , 200);
//            return redirect('/');
        }
    }
    public function show()
    {
        if (! $this->request->expectsJson()) {
            $response = [
                'success' => false,
                'data' => null,
                'message' => 'Error Occured.'
            ];
            return response()->json($response , 404);
//            abort(404);
        }
        // Find the User
        $user = User::whereVerifiedId('yes')
            ->whereId($this->request->id)
            ->where('id', '<>', auth()->id())
            ->firstOrFail();
        // Check if Plan exists
        $plan = $user->plans()
            ->whereInterval($this->request->interval)
            ->firstOrFail();
        // Get Payment Gateway
        $payment = PaymentGateways::findOrFail($this->request->payment_gateway);
        $currencyCodes = [
            'AUD' => 036,
            'CAD' => 124,
            'JPY' => 392,
            'GBP' => 826,
            'USD' => 840,
            'EUR' => 978
        ];
        switch ($plan->interval) {
            case 'weekly':
                $interval = 7;
                break;
            case 'monthly':
                $interval = 30;
                break;
            case 'quarterly':
                $interval = 90;
                break;
            case 'biannually':
                $interval = 180;
                break;
            case 'yearly':
                $interval = 365;
                break;
        }
        $formPrice = Helper::amountGross($plan->price);
        $formInitialPeriod = $interval;
        $formNumRebills = 99;
        $currencyCode = array_key_exists($this->settings->currency_code, $currencyCodes) ? $currencyCodes[$this->settings->currency_code] : 840;
        // Hash
        $hash = md5($formPrice . $formInitialPeriod . $formPrice . $formInitialPeriod . $formNumRebills . $currencyCode . $payment->ccbill_salt);
        // Redirect to CCBill
        $input['clientAccnum']    = $payment->ccbill_accnum;
        $input['clientSubacc']    = $payment->ccbill_subacc;
        $input['initialPrice']    = $formPrice;
        $input['initialPeriod']   = $formInitialPeriod;
        $input['recurringPrice']  = $formPrice;
        $input['recurringPeriod'] = $formInitialPeriod;
        $input['numRebills']      = $formNumRebills;
        $input['formDigest']      = $hash;
        $input['currencyCode']    = $currencyCode;
        $input['type']            = 'subscription';
        $input['creator']         = $user->id ?? null;
        $input['user']            = auth()->id();
        $input['interval']        = $interval;
        $input['planInterval']    = $plan->interval;
        $input['user']            = auth()->id();
        $input['priceOriginal']   = $plan->price;
        $input['taxes']           = auth()->user()->taxesPayable();
        // Base url
        $baseURL = 'https://api.ccbill.com/wap-frontflex/flexforms/' . $payment->ccbill_flexid;
        // Build redirect url
        $inputs = http_build_query($input);
        $redirectUrl = $baseURL . '?' . $inputs;
        $response = [
            'success' => true,
            'data' => [
                'redirect_url' => $redirectUrl,
            ],
            'message' => 'Redirect Url.'
        ];
        return response()->json($response , 200);
//        return response()->json([
//            'success' => true,
//            'url' => $redirectUrl,
//        ]);
    }
}
