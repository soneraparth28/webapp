<?php

namespace App\Http\Controllers\Auth;

use Mail;
use Cookie;
use PHPUnit\TextUI\Help;
use Validator;
use App\Helper;
use App\Models\User;
use App\Models\Countries;
use App\Models\Referrals;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\AdminSettings;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Auth\RegistersUsers;


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

        $messages = array (
            "letters"    => trans('validation.letters'),
            'g-recaptcha-response.required_if' => trans('admin.captcha_error_required'),
            'g-recaptcha-response.captcha' => trans('admin.captcha_error'),
        );

        Validator::extend('ascii_only', function($attribute, $value, $parameters){
            return !preg_match('/[^x00-x7F\-]/i', $value);
        });

        // Validate if have one letter
        Validator::extend('letters', function($attribute, $value, $parameters){
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
        if ($this->settings->registration_active == '1' && $this->settings->home_style == 0)	{
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
        $isProfile = isset($data['isProfile']) ? '?r='.$data['isProfile'] : null;

        // Verify Settings Admin
        if ($this->settings->email_verification == '1') {

            $confirmation_code = Str::random(100);
            $status = 'pending';

            //send verification mail to user
            $_username      = $data['name'];
            $_email_user    = $data['email'];
            $_title_site    = $this->settings->title;
            $_email_noreply = $this->settings->email_no_reply;

            Mail::send('emails.verify', ['confirmation_code' => $confirmation_code, 'isProfile' => $isProfile],
                function($message) use (
                    $_username,
                    $_email_user,
                    $_title_site,
                    $_email_noreply
                ) {
                    $message->from($_email_noreply, $_title_site);
                    $message->subject(trans('users.title_email_verify'));
                    $message->to($_email_user,$_username);
                });

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
            if(!is_null($country)) $countryId = $country->id;
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
            'username' => 'u'.$user->id,
        ]);

        // Verify Settings Admin
        if ($this->settings->email_verification == '1') {

            return response()->json([
                'success' => true,
                'check_account' => trans('auth.check_account'),
            ]);

        } else {
            $this->guard()->login($user);

            return response()->json([
                'success' => true,
                'isLoginRegister' => true,
                'isModal' => $isModal ? true : false,
                'url_return' => url('settings/page'),
            ]);
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
        if(!is_null($country)) $countryId = $country->id;
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
            'username' => 'u'.$user->id,
            
        ]);
        
        $this->guard()->login($user);
        
        return response()->json([
            'success' => true,
            'isLoginRegister' => true,
            'isModal' => $isModal ? true : false,
            'url_return' => url('settings/page'),
        ]);

    }
    
    public function courseInvitationRegister (Request $request)
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
        if(!is_null($country)) $countryId = $country->id;
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
            'creator_referred_id' => null
        ]);

        // Update Username
        $user->update([
            'username' => 'u'.$user->id,
            
        ]);
        
        $this->guard()->login($user);
        
        return response()->json([
            'success' => true,
            'isLoginRegister' => true,
            'isModal' => $isModal ? true : false,
            'url_return' => url('settings/page'),
        ]);

    }
}