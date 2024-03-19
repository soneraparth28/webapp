<?php /** @noinspection ALL */

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\PaymentGateways;
use Illuminate\Support\Facades\DB;
use Stripe\StripeClient;
use Stripe\Exception\ApiErrorException;
use Illuminate\Support\Str;

class StripeConnectController extends Controller
{
    public function redirectToStripe()
    {
        $payment = PaymentGateways::whereName('Stripe')->first();
        $stripe = new StripeClient($payment->key_secret);
        $user = User::findOrFail(auth()->id());
        // Complete the onboarding process
        if (! $user->completed_stripe_onboarding) {
            $token = Str::random();
            DB::table('stripe_state_tokens')->insert([
                'user_id'  => $user->id,
                'token'    => $token,
                'created_at' => now(),
                'updated_at' => now()
            ]);
            try {
                // Let's check if they have a stripe connect id
                if (is_null($user->stripe_connect_id)) {
                    // Create account
                    $account = $stripe->accounts->create([
                        'country' => $user->country()->country_code,
                        'type'    => 'express',
                        'email'   => $user->email,
                    ]);
                    $user->update(['stripe_connect_id' => $account->id]);
                    $user->fresh();
                }
                $onboardLink = $stripe->accountLinks->create([
                    'account'     => $user->stripe_connect_id,
                    'refresh_url' => route('redirect.stripe'),
                    'return_url'  => route('save.stripe', ['token' => $token]),
                    'type'        => 'account_onboarding'
                ]);
                $response = [
                    'success' => true,
                    'data' => [
                        'redirect_url' => $onboardLink->url,
                    ],
                    'message' => 'Redirect Link',
                ];
                return response()->json($response , 200);
//                return redirect($onboardLink->url);
            } catch (\Exception $exception){
                DB::table('stripe_state_tokens')->where('token', '=', $token)->delete();
                $response = [
                    'success' => false,
                    'data' => null,
                    'message' => $exception->getMessage(),
                ];
                return response()->json($response , 404);
//                return back()->withError($exception->getMessage()) ;
            }
        }
        try {
            $loginLink = $stripe->accounts->createLoginLink($user->stripe_connect_id);
            $response = [
                'success' => true,
                'data' => [
                    'redirect_url' => $loginLink->url,
                ],
                'message' => 'Redirect Link',
            ];
            return response()->json($response , 200);
//            return redirect($loginLink->url);
        } catch (\Exception $exception){
            $response = [
                'success' => false,
                'data' => null,
                'message' => $exception->getMessage(),
            ];
            return response()->json($response , 404);
//            return back()->withError($exception->getMessage()) ;
        }
    }
    public function saveStripeAccount($token)
    {
        $stripeToken = DB::table('stripe_state_tokens')->where('token', '=', $token)->first();
        if(is_null($stripeToken)) {
            $response = [
                'success' => false,
                'data' => null,
                'message' => 'Error Occurred.',
            ];
            return response()->json($response , 404);
//            abort(404);
        }
        $user = User::find($stripeToken->user_id);
        $user->update([
            'completed_stripe_onboarding' => true
        ]);
        $response = [
            'success' => true,
            'data' => null,
            'message' => 'general.stripe_connect_setup_success',
        ];
        return response()->json($response , 200);
//        return redirect('settings/payout/method')->withStatus(__('general.stripe_connect_setup_success'));
    }
}
