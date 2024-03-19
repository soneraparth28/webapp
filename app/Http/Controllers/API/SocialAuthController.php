<?php /** @noinspection ALL */

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\SocialAccountService;
use Socialite;

class SocialAuthController extends Controller
{
    public function redirect($provider)
    {
        // $response = [
        //     'success' => true,
        //     'data' => null,
        //     'message' => 'Socialite::driver($provider)->redirect()',
        // ];
        // return response()->json($response , 200);
        return Socialite::driver($provider)->redirect();
    }
    public function callback(SocialAccountService $service, Request $request, $provider)
    {
        try {
            $user = $service->createOrGetUser(Socialite::driver($provider)->user(), $provider);
            // Return Error missing Email User
            if( ! isset($user->id)) {
                $response = [
                    'success' => true,
                    'data' => [
                        'user' => $user
                    ],
                    'message' => 'User Data.',
                ];
                return response()->json($response , 200);
//                return $user;
            } else {
                auth()->login($user);
            }

        } catch (\Exception $e) {
            dd($e->getMessage());
            $response = [
                'success' => false,
                'data' => [
                    'redirect_url' => 'login',
                ],
                'message' => $e->getMessage(),
            ];
            return response()->json($response , 400);
//            return redirect('login')->with(['login_required' => $e->getMessage()]);
        }
        $success['token'] =  $user->createToken('MyApp')-> accessToken;
        $response = [
            'success' => true,
            'data' => [
                'redirect_url' => '/',
                'user' => $success
            ],
            'message' => 'Redirect Url',
        ];
        return response()->json($response , 200);
//        return redirect()->to('/');
    }
}
