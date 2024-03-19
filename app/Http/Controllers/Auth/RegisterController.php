<?php

namespace App\Http\Controllers\Auth;

use Mail;
use Cookie;
use PHPUnit\TextUI\Help;
use Validator;
use App\Helper;
use App\Models\User;
use App\Models\Countries;
use App\Models\Transactions;
use App\Models\Updates;
use App\Models\Plans;
use App\Models\Referrals;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\AdminSettings;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Auth\RegistersUsers;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;


class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after login / registration.
     *
     * @var string
     */
    protected $redirectTo = '/';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(AdminSettings $settings)
    {
        $this->middleware('guest');
        $this->settings = $settings::first();
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {

        $data['_captcha'] = $this->settings->captcha;

        $messages = array(
            "letters"    => trans('validation.letters'),
            'g-recaptcha-response.required_if' => trans('admin.captcha_error_required'),
            'g-recaptcha-response.captcha' => trans('admin.captcha_error'),
        );

        Validator::extend('ascii_only', function ($attribute, $value, $parameters) {
            return !preg_match('/[^x00-x7F\-]/i', $value);
        });

        // Validate if have one letter
        Validator::extend('letters', function ($attribute, $value, $parameters) {
            return preg_match('/[a-zA-Z0-9]/', $value);
        });

        return Validator::make($data, [
            'name' => 'required|string|max:100',
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|min:6',
            'agree_gdpr' => 'required',
            'g-recaptcha-response' => 'required_if:_captcha,==,on|captcha'
        ], $messages);
    }

    /**
     * Show registration form.
     */
    public function showRegistrationForm()
    {
        if ($this->settings->registration_active == '1' && $this->settings->home_style == 0) {
            return view('auth.register');
        } else {
            return redirect('/');
        }
    }


    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return User
     */
    protected function create(array $data)
    {
        $isProfile = isset($data['isProfile']) ? '?r=' . $data['isProfile'] : null;

        // Verify Settings Admin
        if ($this->settings->email_verification == '1') {

            $confirmation_code = Str::random(100);
            $status = 'pending';

            //send verification mail to user
            $_username      = $data['name'];
            $_email_user    = $data['email'];
            $_title_site    = $this->settings->title;
            $_email_noreply = $this->settings->email_no_reply;

            Mail::send(
                'emails.verify',
                ['confirmation_code' => $confirmation_code, 'isProfile' => $isProfile],
                function ($message) use (
                    $_username,
                    $_email_user,
                    $_title_site,
                    $_email_noreply
                ) {
                    $message->from($_email_noreply, $_title_site);
                    $message->subject(trans('users.title_email_verify'));
                    $message->to($_email_user, $_username);
                }
            );
        } else {
            $confirmation_code = '';
            $status            = 'active';
        }

        if ($this->settings->account_verification == '1') {
            $verify = 'no';
        } else {
            $verify = 'yes';
        }

        $token = Str::random(75);

        // Get user country
        $country = Countries::whereCountryCode(Helper::userCountry())->first();
        if (!is_null($country)) $countryId = $country->id;
        else $countryId = Countries::whereCountryCode("DK")->first()->id; //Denmark default



        return User::create([
            'username'          => Helper::strRandom(),
            'countries_id'      => $countryId,
            'name'              => $data['name'],
            'email'             => strtolower($data['email']),
            'password'          => bcrypt($data['password']),
            'avatar'            => $this->settings->avatar,
            'cover'             => $this->settings->cover_default ?? '',
            'status'            => $status,
            'role'              => 'normal',
            'permission'        => 'none',
            'confirmation_code' => $confirmation_code,
            'oauth_uid'         => '',
            'oauth_provider'    => '',
            'token'             => $token,
            'story'             => trans('users.story_default'),
            'verified_id'       => $verify,
            'ip'                => request()->ip(),
            'language'          => session('locale')
        ]);
    }

    /**
     * Handle a registration request for the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request)
    {
        $validator = $this->validator($request->all());
        $isModal   = $request->input('isModal');

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->getMessageBag()->toArray(),
            ]);
        }

        event(new Registered($user = $this->create($request->all())));


        // Check Referral
        if ($this->settings->referral_system == 'on') {

            $referredBy = User::find(Cookie::get('referred'));

            if ($referredBy) {
                Referrals::create([
                    'user_id' => $user->id,
                    'referred_by' => $referredBy->id,
                ]);
            }
        }

        // Update Username
        $user->update([
            'username' => 'u' . $user->id,
        ]);

        // Verify Settings Admin
        if ($this->settings->email_verification == '1') {

            return response()->json([
                'success' => true,
                'check_account' => trans('auth.check_account'),
                'omni_api_response' => $this->omni_send_api($user,'user'),
            ]);
        } else {
            $this->guard()->login($user);

            return response()->json([
                'success' => true,
                'isLoginRegister' => true,
                'isModal' => $isModal ? true : false,
                'url_return' => url('settings/page'),
                'omni_api_response' => $this->omni_send_api($user,'user'),
            ]);
        }
    }

    public function omni_send_api($user,$type)
    {
        try {
            $apiKey = '6543b043bd91625835f85cd7-ChqnYHLY1gFu58vhxOoeOGNEAy3iNLkreKK7V0ysSrXlyHCQaI';
            $apiEndpoint = 'https://api.omnisend.com/v3/contacts'; // Adjust the endpoint as needed
    
            // Data to be sent in the API call
            $data = [
                'email' => $user->email, // Replace with the user's email
                'tags' => [$type],
                'status' => 'subscribed',
                'statusDate' => now()->toIso8601String(),
            ];
    
            // Make the API call using Laravel HTTP client
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'X-API-Key' => $apiKey,
            ])->post($apiEndpoint, $data);
    
            // Handle the response
            $responseData = $response->json();
    
            // You can return the response data or perform any additional actions
            return response()->json($responseData);
        } catch (\Throwable $th) {
            return $th->getMessage();
        }
    }

    public function showCreatorRegistrationForm()
    {
        // return view('auth.creator-register');
        return view('auth.signupuser');
    }

    public function creatorRegister(Request $request)
    {

        // echo "ddsad";
        // exit();

        $validator = $this->validator($request->all());
        $isModal   = $request->input('isModal');

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->getMessageBag()->toArray(),
            ]);
        }

        $creator_referred_id = Cookie::get('cc_referred');
        $creator_referred_id = (!empty($creator_referred_id)) ? $creator_referred_id : null;

        $confirmation_code = '';
        $status = 'active';
        $verify = 'yes';
        $token = Str::random(75);

        // Get user country
        $country = Countries::whereCountryCode(Helper::userCountry())->first();
        if (!is_null($country)) $countryId = $country->id;
        else $countryId = Countries::whereCountryCode("DK")->first()->id; //Denmark default

        $user = User::create([
            'username'          => Helper::strRandom(),
            'countries_id'      => $countryId,
            'name'              => $request->name,
            'email'             => strtolower($request->email),
            'password'          => bcrypt($request->password),
            'avatar'            => $this->settings->avatar,
            'cover'             => $this->settings->cover_default ?? '',
            'status'            => $status,
            'role'              => 'normal',
            'permission'        => 'none',
            'confirmation_code' => $confirmation_code,
            'oauth_uid'         => '',
            'oauth_provider'    => '',
            'token'             => $token,
            'story'             => trans('users.story_default'),
            'verified_id'       => $verify,
            'ip'                => request()->ip(),
            'language'          => session('locale'),
            'creator_referred_id' => $creator_referred_id
        ]);

        // Update Username
        $user->update([
            'username' => 'u' . $user->id,
            'omni_api_response' => $this->omni_send_api($user,'creator'),
        ]);

        $this->guard()->login($user);
        
          $id = Auth::id();

        // Auth::logout();

        return response()->json([
            'success' => true,
            'isLoginRegister' => true,
            'isModal' => $isModal ? true : false,
            'url_return' => url('settings/page'),
            'omni_api_response' => $this->omni_send_api($user,'creator'),
            'login_user' => $id,
        ]);
    }

    public function courseInvitationRegister(Request $request)
    {

        // echo "ddsad";
        // exit();

        $validator = $this->validator($request->all());
        $isModal   = $request->input('isModal');

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->getMessageBag()->toArray(),
            ]);
        }

        $confirmation_code = '';
        $status = 'active';
        $verify = 'no';
        $token = Str::random(75);

        // Get user country
        $country = Countries::whereCountryCode(Helper::userCountry())->first();
        if (!is_null($country)) $countryId = $country->id;
        else $countryId = Countries::whereCountryCode("DK")->first()->id; //Denmark default

        $user = User::create([
            'username'          => Helper::strRandom(),
            'countries_id'      => $countryId,
            'name'              => $request->name,
            'email'             => strtolower($request->email),
            'password'          => bcrypt($request->password),
            'avatar'            => $this->settings->avatar,
            'cover'             => $this->settings->cover_default ?? '',
            'status'            => $status,
            'role'              => 'normal',
            'permission'        => 'none',
            'confirmation_code' => $confirmation_code,
            'oauth_uid'         => '',
            'oauth_provider'    => '',
            'token'             => $token,
            'story'             => trans('users.story_default'),
            'verified_id'       => 'yes',
            'ip'                => request()->ip(),
            'language'          => session('locale'),
            'creator_referred_id' => null
        ]);

        // Update Username
        $user->update([
            'username' => 'u' . $user->id,

        ]);

        $this->guard()->login($user);
        
      
        return response()->json([
            'success' => true,
            'isLoginRegister' => true,
            'isModal' => $isModal ? true : false,
            'url_return' => url('settings/page'),
            
        ]);
    }
    
   
    
   public function update_transaction(Request $request){
        
        // return $request->user_id;
        $user_id = $request->user_id;
        $price = $request->price;
        $user = User::where ('id', '=', $user_id)->first();
       
        $trans = new Transactions();
        $trans->txn_id = str_random(25);
        $trans->user_id = $user_id;
        $trans->subscriptions_id = 0;
        $trans->amount = $price;
        $trans->earning_net_user = '4.25';
        $trans->earning_net_admin = '0.75';
        $trans->payment_gateway = 'Revolut';
        $trans->percentage_applied = '15%';
        $trans->invoice_name = $user->name;
        $trans->invoice_email = $user->email;
        $trans->taxes = '1';
        $trans->save();
            
            $plans = new Plans();
            $plans->user_id = $user_id ;
            if($price == "99"){
                $plans->name = 'user_'.$user_id.'_monthly';
            }else{
                 $plans->name = 'user_'.$user_id.'_yearly';
            }
            $plans->price = $price;

            if($price == "99"){
                $plans->interval = 'monthly';
                $interval = 'monthly';
            }else{
                 $plans->interval = 'yearly';
                 $interval = 'yearly';
            }

            if($plans && $plans->ends_at) {

                $current_date_str = strtotime($plans->ends_at);
                
                if ($current_date_str === false) {
                    $current_date_str = strtotime(date('Y-m-d'));
                }
            }else {
                $current_date_str = strtotime(date('Y-m-d'));
            }
             
            $end_date_for_revolut = date('Y-m-d', strtotime("+1 month", $current_date_str));
         
            if($interval == 'weekly') { $end_date_for_revolut = date('Y-m-d', strtotime("+1 week", $current_date_str)); }
            if($interval == 'quarterly') { $end_date_for_revolut = date('Y-m-d', strtotime("+3 month", $current_date_str)); }
            if($interval == 'biannually') { $end_date_for_revolut = date('Y-m-d', strtotime("+6 month", $current_date_str)); }
            if($interval == 'yearly') { $end_date_for_revolut = date('Y-m-d', strtotime("+12 month", $current_date_str)); }
            
            $plans->ends_at = $end_date_for_revolut;
            $plans->is_cancelled = '0';
            $plans->status = '1';
            $plans->save();
        Auth::loginUsingId($user->id);
       return "ok";
        
    } 
    public function update_transaction_old(Request $request){
        
        // return $request->user_id;
         $user_id = $request->user_id;
        $price = $request->price;
        $user = User::where ('id', '=', $user_id)->first();
       
        $trans = new Transactions();
        $trans->txn_id = str_random(25);
        $trans->user_id = $user_id;
        $trans->subscriptions_id = 0;
        $trans->amount = $price;
        $trans->earning_net_user = '4.25';
        $trans->earning_net_admin = '0.75';
        $trans->payment_gateway = 'Revolut';
        $trans->percentage_applied = '15%';
        $trans->invoice_name = $user->name;
        $trans->invoice_email = $user->email;
        $trans->taxes = '1';
        $trans->save();
        
        
        
            // $post = new Updates();
            // $post->user_id = $user_id ;
            // $post->date = Carbon::now();
            // $post->status = 'active';
            // $post->course = "yes";
            // $post->title = 'SaaS Mentorship';
            // $post->description = "Are you eager to delve into the world of Software as a Service (SaaS) and gain a deep understanding of the latest tech trends? Look no further! Our comprehensive course is designed to equip you with the knowledge and skills needed to thrive in the dynamic SaaS industry. Discover the essentials of SaaS architecture, development methodologies, customer acquisition strategies, and much more. Whether you're a tech enthusiast, aspiring entrepreneur, or industry professional, this course is your gateway to mastering the ins and outs of SaaS.";
            // $post->token_id = Str::random(150);
            // $post->locked = 'yes';
            // $post->price = $price;
            // $post->update_category_id = NULL;
            // $post->access = 'paid';
            // $post->strict_flow = "yes";
            // $post->media_downloadable = "no";
            // $post->image = '303650ecdc67c4561695468998isa86jjuzayaliyjurfo.jpg';
            // $post->save();
            
            $plans = new Plans();
            $plans->user_id = $user_id ;
            if($price == "99"){
                $plans->name = 'user_'.$user_id.'_monthly';
            }else{
                 $plans->name = 'user_'.$user_id.'_yearly';
            }
            $plans->price = $price;

            if($price == "99"){
                $plans->interval = 'monthly';
                $interval = 'monthly';
            }else{
                 $plans->interval = 'yearly';
                 $interval = 'yearly';
            }

            if($plans && $plans->ends_at) {

                $current_date_str = strtotime($plans->ends_at);
                
                if ($current_date_str === false) {
                    $current_date_str = strtotime(date('Y-m-d'));
                }
            }else {
                $current_date_str = strtotime(date('Y-m-d'));
            }
             
            $end_date_for_revolut = date('Y-m-d', strtotime("+1 month", $current_date_str));
         
            if($interval == 'weekly') { $end_date_for_revolut = date('Y-m-d', strtotime("+1 week", $current_date_str)); }
            if($interval == 'quarterly') { $end_date_for_revolut = date('Y-m-d', strtotime("+3 month", $current_date_str)); }
            if($interval == 'biannually') { $end_date_for_revolut = date('Y-m-d', strtotime("+6 month", $current_date_str)); }
            if($interval == 'yearly') { $end_date_for_revolut = date('Y-m-d', strtotime("+12 month", $current_date_str)); }
            
            $plans->ends_at = $end_date_for_revolut;
            $plans->is_cancelled = '0';
            $plans->status = '1';
            $plans->save();
        
        
        
        
        Auth::loginUsingId($user->id);
        
       return "ok";
    // return auth::check();
        
        //  $this->guard()->setUser($user);
         
        //  return redirect('settings/page');
         
        // return User::find($user_id)->first();
        // Auth::loginUsingId($user_id);
        
        // Auth::guard('web')->attempt(['email' => $user->email, 'password' => $user->password]);
        
        // Auth::guard('admin')->attempt($request->only('email', 'password'));
        
        // if (Auth::check()){

        //  return  '<p>User is login.</p>';
        
        // }else{
        
        // return  '<p>User is not login.</p>';
        
        // }        

        
        
        
    }
}
