<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\AdminSettings;
use App\Models\PaymentGateways;
use Yabacon\Paystack;
use Yabacon\Paystack\Event;
use App\Models\Subscriptions;
use App\Models\Transactions;
use App\Models\Notifications;
use App\Models\Conversations;
use App\Models\Deposits;
use App\Models\Plans;
use App\Models\Messages;
use App\Models\User;
use Carbon\Carbon;
use App\Helper;
use Mail;
use App\Http\Controllers\Traits;

class PaystackController extends Controller
{
    use Traits\Functions;

    public function __construct(AdminSettings $settings, Request $request) {
        $this->settings = $settings::first();
        $this->request = $request;
    }
    public function cardAuthorizationVerify()
    {
        $pystk = PaymentGateways::whereName('Paystack')->whereEnabled(1)->firstOrFail();
        if(! $this->request->reference) {
            $response = [
                'success' => false,
                'data' => null,
                'message' => 'No reference supplied.',
            ];
            return response()->json($response , 400);
            // die('No reference supplied');
        }
        // initiate the Library's Paystack Object
        $paystack = new Paystack($pystk->key_secret);
        try
        {
            // verify using the library
            $tranx = $paystack->transaction->verify([
                'reference' => $this->request->reference, // unique to transactions
            ]);
        }catch(\Exception $e) {
            $response = [
                'success' => false,
                'data' => null,
                'message' => $e->getMessage(),
            ];
            return response()->json($response , 400);
            // die($e->getMessage());
        }
        if('success' === $tranx->data->status) {
            $user = User::find(auth()->user()->id);
            $user->paystack_authorization_code = $tranx->data->authorization->authorization_code;
            $user->paystack_last4 = $tranx->data->authorization->last4;
            $user->paystack_exp = $tranx->data->authorization->exp_month . '/' .$tranx->data->authorization->exp_year;
            $user->paystack_card_brand = trim($tranx->data->authorization->card_type);
            $user->save();
        }
        $response = [
            'success' => true,
            'data' => null,
            'message' => 'Success.',
        ];
        return response()->json($response , 200);
        // return redirect('my/cards')->withSuccessMessage(trans('general.success'));
    }
    public function show()
    {
        if(! $this->request->expectsJson()) {
            $response = [
                'success' => false,
                'data' => null,
                'message' => 'Error Occured.',
            ];
            return response()->json($response , 404);
            // abort(404);
        }
        if(auth()->user()->paystack_authorization_code == '') {
            $response = [
                'success' => false,
                'data' => null,
                'message' => trans('general.please_add_payment_card'),
            ];
            return response()->json($response , 400);
            // return response()->json([
            //     "success" => false,
            //     'errors' => ['error' => trans('general.please_add_payment_card')]
            // ]);
        }
        // Find the user to subscribe
        $user = User::whereVerifiedId('yes')->whereId($this->request->id)->where('id', '<>', auth()->user()->id)->firstOrFail();
        // Check if Plan exists
        $plan = $user->plans()->whereInterval($this->request->interval)->firstOrFail();
        $payment = PaymentGateways::whereName('Paystack')->whereEnabled(1)->firstOrFail();
        try {
            // initiate the Library's Paystack Object
            $paystack = new Paystack($payment->key_secret);
            //========== Create Plan if no exists
            if(! $plan->paystack) {
                switch($plan->interval) {
                    case 'weekly':
                        $interval = 'weekly';
                        break;
                    case 'monthly':
                        $interval = 'monthly';
                        break;
                    case 'quarterly':
                        $interval = 'quarterly';
                        break;
                    case 'biannually':
                        $interval = 'biannually';
                        break;
                    case 'yearly':
                        $interval = 'annually';
                        break;
                }
                $userPlan = $paystack->plan->create([
                    'name'=> trans('general.subscription_for').' @'.$user->username,
                    'amount'=> ($plan->price*100),
                    'interval'=> $interval,
                    'currency'=> $this->settings->currency_code
                ]);
                $planCode = $userPlan->data->plan_code;
                // Insert Plan Code to User
                $plan->paystack = $planCode;
                $plan->save();
            } else {
                $planCode = $plan->paystack;
                try {
                    $planCurrent = $paystack->plan->fetch(['id' => $planCode]);
                    $pricePlanOnPaystack = ($planCurrent->data->amount / 100);
                    // We check if the plan changed price
                    if($pricePlanOnPaystack != $plan->price) {
                        // Update price
                        $paystack->plan->update([
                            'name'=> trans('general.subscription_for').' @'.$user->username,
                            'amount'=> ($plan->price*100),
                            ],['id' => $planCode]);
                    }

                } catch(\Exception $e) {
                    $response = [
                        'success' => false,
                        'data' => null,
                        'message' => $e->getMessage(),
                    ];
                    return response()->json($response , 404);
                    // return response()->json([
                    //     "success" => false,
                    //     'errors' => ['error' => $e->getMessage()]
                    // ]);
                }

            }
            //========== Create Subscription
            $subscr = $paystack->subscription->create([
                      'plan'=> $planCode,
                      'customer'=> auth()->user()->email,
                      'authorization'=> auth()->user()->paystack_authorization_code
                    ]);
            $subscription = new Subscriptions();
            $subscription->user_id = auth()->user()->id;
            $subscription->stripe_price = $plan->name;
            $subscription->subscription_id = $subscr->data->subscription_code;
            $subscription->ends_at = now();
            $subscription->interval = $plan->interval;
            $subscription->save();
            // Send Email to User and Notification
            Subscriptions::sendEmailAndNotify(auth()->user()->name, $user->id);
        } catch(\Exception $exception) {
            $response = [
                'success' => false,
                'data' => null,
                'message' => $exception->getMessage(),
            ];
            return response()->json($response , 404);
            // return response()->json([
            //     'success' => false,
            //     'errors' => ['error' => $exception->getMessage()]
            // ]);
        }
        $response = [
            'success' => true,
            'data' => [
                'redirect_url' => url('buy/subscription/success', $user->username),
            ],
            'message' => 'Redirect Url.',
        ];
        return response()->json($response , 200);
        // return response()->json([
        //     'success' => true,
        //     'url' => url('buy/subscription/success', $user->username)
        // ]);
    }
}
