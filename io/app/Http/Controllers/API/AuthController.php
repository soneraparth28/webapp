<?php /** @noinspection ALL */

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        if(Auth::attempt(['email' => $request->email, 'password' => $request->password])){
            $user = Auth::user();
            $success['token'] =  $user->createToken('MyApp')-> accessToken;
            $success['name'] =  $user->name;
            $response = [
                'success' => true,
                'data'    => [
                    'user' => $success,
                ],
                'message' => 'User login successfully.',
            ];


            return response()->json($response , 200);
        }
        else{
            $response = [
                'success' => false,
                'data' => null,
                'message' => 'User Login Error.',
            ];
            return response()->json($response , 400);
        }
    }
    public function register (Request $request) 
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);
        if ($validator->fails())
        {
            return response(['success' => true,'errors'=>$validator->errors()->all()], 422);
        }
        $request['password']=Hash::make($request['password']);
        $request['remember_token'] = Str::random(10);
        $user = User::create($request->toArray());
        $token = $user->createToken('Laravel Password Grant Client')->accessToken;
        $response = ['token' => $token];
        return response($response, 200);
    }
    public function logout(){   
    if (Auth::check()) {
        Auth::user()->token()->revoke();
        return response()->json(['success' =>'logout_success'],200); 
    }else{
        return response()->json(['error' =>'api.something_went_wrong'], 500);
    }
}
}
