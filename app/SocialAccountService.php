<?php

namespace App;

use Cookie;
use App\Models\User;
use App\Models\AdminSettings;
use App\Models\Countries;
use App\Models\Referrals;
use Illuminate\Support\Str;
use App\Helper;
use Laravel\Socialite\Contracts\User as ProviderUser;

class SocialAccountService
{
    public function createOrGetUser(ProviderUser $providerUser, $provider )
    {
      $settings = AdminSettings::first();

      $user = User::whereOauthProvider($provider)
          ->whereOauthUid($providerUser->getId())
          ->first();

      if (empty($user)) {


        //return 'Error! Your email is required, Go to app settings and delete our app and try again';
        if (empty($providerUser->getEmail())) {
          return redirect("login")->with(array('login_required' => trans('error.error_required_mail')));
          exit;
        }

        // //Verify Email user
        $userEmail = User::whereEmail($providerUser->getEmail())->first();

        if (!empty($userEmail)) {
          return redirect("login")->with(array('login_required' => trans('error.mail_exists')));
          exit;
        }

        $token = Str::random(75);

        $avatar = $settings->avatar;
        $nameAvatar = time().$providerUser->getId();
        $path = config('path.avatar');



        if (! empty($providerUser->getAvatar())) {
          
          // Get Avatar Large Facebook

          // echo $provider;
          // exit();
          if ($provider == 'facebook') {
            
            $avatarUser = str_replace('?type=normal', '?type=large', $providerUser->getAvatar());

            $fileContents = file_get_contents($avatarUser);

            \Storage::put($path.$nameAvatar.'.jpg', $fileContents, 'public');

            $avatar = $nameAvatar.'.jpg';

          }

          // Get Avatar Large Twitter
          if ($provider == 'twitter') {
            $avatarUser = str_replace('_normal', '_200x200', $providerUser->getAvatar());

            $fileContents = file_get_contents($avatarUser);

            \Storage::put($path.$nameAvatar.'.jpg', $fileContents, 'public');

            $avatar = $nameAvatar.'.jpg';
          }

          // Get Avatar Google
          if ($provider == 'google') {
            $fileContents = file_get_contents($providerUser->getAvatar());

            \Storage::put($path.$nameAvatar.'.jpg', $fileContents, 'public');

            $avatar = $nameAvatar.'.jpg';
          }

        } // Empty getAvatar()

            if ($settings->account_verification == '1') {
              $verify = 'no';
            } else {
              $verify = 'yes';
            }



        // Get user country
        $country = Countries::whereCountryCode(Helper::userCountry())->first();

				$user = User::create([
                'username'          => Helper::strRandom(),
                'countries_id'      => $country->id ?? '',
                'name'              => $providerUser->getName(),
                'email'             => strtolower($providerUser->getEmail()),
                'password'          => '',
                'avatar'            => $avatar,
                'cover'             => $settings->cover_default ?? '',
                'status'            => 'active',
                'role'              => 'normal',
                'permission'        => 'none',
                'confirmation_code' => '',
                'oauth_uid'         => $providerUser->getId(),
                'oauth_provider'    => $provider,
                'token'             => $token,
                'story'             => trans('users.story_default'),
                'verified_id'       => $verify,
                'ip'                => request()->ip(),
                'language'          => session('locale')
			]);

      // Check Referral
      if ($settings->referral_system == 'on') {

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

      return $user;

    }// !$user
        return $user;
    }
}
