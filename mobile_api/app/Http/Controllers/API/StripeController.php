<?php /** @noinspection ALL */

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Mail;
use App\Helper;
use Carbon\Carbon;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\AdminSettings;
use App\Models\Subscriptions;
use App\Models\Notifications;
use App\Models\PaymentGateways;
use App\Models\Transactions;
use Laravel\Cashier\Exceptions\IncompletePayment;

class StripeController extends Controller
{
    public function __construct(AdminSettings $settings, Request $request) {
        $this->settings = $settings::first();
        $this->request = $request;
    }
    protected function show()
    {
        if(! $this->request->expectsJson()) {
            $response = [
                'success' => false,
                'data' => null,
                'message' => 'Error Occurred.',
            ];
            return response()->json($response , 404);
//            abort(404);
        }
        if(! auth()->user()->hasPaymentMethod()) {
            $response = [
                'success' => false,
                'data' => null,
                'message' => trans('general.please_add_payment_card'),
            ];
            return response()->json($response , 404);
//            return response()->json([
//                "success" => false,
//                'errors' => ['error' => trans('general.please_add_payment_card')]
//            ]);
        }
        // Find the user to subscribe
        $user = User::whereVerifiedId('yes')->whereId($this->request->id)->where('id', '<>', auth()->id())->firstOrFail();
        // Check if Plan exists
        $plan = $user->plans()->whereInterval($this->request->interval)->firstOrFail();
        $payment = PaymentGateways::whereName('Stripe')->whereEnabled(1)->first();
        $stripe = new \Stripe\StripeClient($payment->key_secret);
        $userPlan = $plan->name;
        // Verify Plan Exists
        try {
            $planCurrent = $stripe->plans->retrieve($userPlan, []);
            $pricePlanOnStripe = ($planCurrent->amount / 100);
            // We check if the plan changed price
            if ($pricePlanOnStripe != $plan->price) {
                // Delete old plan
                $stripe->plans->delete($userPlan, []);
                // Delete Product
                $stripe->products->delete($planCurrent->product, []);
                // We create the plan with new price
                $this->createPlan($payment->key_secret, $plan, $user);
            }
        } catch (\Exception $exception) {
            // Create New Plan
            $this->createPlan($payment->key_secret, $plan, $user);
        }
        try {
            // Check Payment Incomplete
            if (auth()->user()->userSubscriptions()->where('stripe_price', $userPlan)->whereStripeStatus('incomplete')->first()) {
                $response = [
                    'success' => false,
                    'data' => null,
                    'message' => trans('general.please_confirm_payment'),
                ];
                return response()->json($response , 404);
//                return response()->json([
//                    "success" => false,
//                    'errors' => ['error' => trans('general.please_confirm_payment')]
//                ]);
            }
            // Create New subscription
            $metadata = [
                'interval' => $plan->interval,
                'taxes' => auth()->user()->taxesPayable()
            ];
            auth()->user()->newSubscription('main', $userPlan)->withMetadata($metadata)->create();
            // Send Email to User and Notification
            Subscriptions::sendEmailAndNotify(auth()->user()->name, $user->id);
            $response = [
                'success' => true,
                'data' => [
                    'redirect_url' => 'buy/subscription/success',
                    'user_name' => $user->username,
                ],
                'message' => 'Redirect Url.',
            ];
            return response()->json($response , 200);
//            return response()->json([
//                'success' => true,
//                'url' => url('buy/subscription/success', $user->username)
//            ]);
        } catch (IncompletePayment $exception) {
            // Insert ID Last Payment
            $subscriptions = Subscriptions::whereUserId(auth()->id())->whereStripePrice($userPlan)->whereStripeStatus('incomplete')->first();
            $subscriptions->last_payment = $exception->payment->id;
            $subscriptions->save();
            $response = [
                'success' => true,
                'data' => [
                    'redirect_url' => 'stripe/payment',
                    'payment_id' => $exception->payment->id,
                ],
                'message' => 'Redirect Url.',
            ];
            return response()->json($response , 200);
//            return response()->json([
//                'success' => true,
//                'url' => url('stripe/payment', $exception->payment->id), // Redirect customer to page confirmation payment (SCA)
//            ]);
        } catch (\Exception $exception) {
            \Log::debug($exception);
            $response = [
                'success' => false,
                'data' => null,
                'message' => $exception->getMessage(),
            ];
            return response()->json($response , 404);
//            return response()->json([
//                'success' => false,
//                'errors' => ['error' => $exception->getMessage()]
//            ]);
        }
    }
}
