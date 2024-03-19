<?php

namespace App\Models;

use Illuminate\Support\Facades\Auth;
use Laravel\Cashier\Billable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Notifications\ResetPassword as ResetPasswordNotification;
use Illuminate\Contracts\Translation\HasLocalePreference;
use App\Models\Notifications;
use Carbon\Carbon;
use Laravel\Passport\HasApiTokens;

/**
 * App\Models\User
 *
 * @property int $id
 * @property string $name
 * @property string $username
 * @property string $countries_id
 * @property string $password
 * @property string $email
 * @property \Illuminate\Support\Carbon $date
 * @property string $avatar
 * @property string $cover
 * @property string $status
 * @property string $role
 * @property string $permission
 * @property string $remember_token
 * @property string $token
 * @property string $confirmation_code
 * @property string $paypal_account
 * @property string $payment_gateway
 * @property string $bank
 * @property string $featured
 * @property string|null $featured_date
 * @property string $about
 * @property string $story
 * @property string $profession
 * @property string $oauth_uid
 * @property string $oauth_provider
 * @property string $categories_id
 * @property string $website
 * @property string|null $stripe_id
 * @property string|null $pm_type
 * @property string|null $pm_last_four
 * @property string|null $trial_ends_at
 * @property string $price
 * @property string $balance
 * @property string|null $verified_id
 * @property string $address
 * @property string $city
 * @property string $zip
 * @property string $facebook
 * @property string $twitter
 * @property string $instagram
 * @property string $youtube
 * @property string $pinterest
 * @property string $github
 * @property string|null $last_seen
 * @property string $email_new_subscriber
 * @property string $plan
 * @property string $notify_new_subscriber
 * @property string $notify_liked_post
 * @property string $notify_commented_post
 * @property string $company
 * @property string $post_locked
 * @property string $ip
 * @property string $dark_mode
 * @property string $gender
 * @property string $birthdate
 * @property string $allow_download_files
 * @property string $language
 * @property string $free_subscription
 * @property string $wallet
 * @property string $tiktok
 * @property string $snapchat
 * @property string $paystack_plan
 * @property string $paystack_authorization_code
 * @property int $paystack_last4
 * @property string $paystack_exp
 * @property string $paystack_card_brand
 * @property string $notify_new_tip
 * @property string $hide_profile
 * @property string $hide_last_seen
 * @property string $last_login
 * @property string $hide_count_subscribers
 * @property string $hide_my_country
 * @property string $show_my_birthdate
 * @property string $notify_new_post
 * @property string $notify_email_new_post
 * @property int $custom_fee
 * @property string $hide_name
 * @property string $birthdate_changed
 * @property string $email_new_tip
 * @property string $email_new_ppv
 * @property string $notify_new_ppv
 * @property string $active_status_online
 * @property string $payoneer_account
 * @property string $zelle_account
 * @property string $notify_liked_comment
 * @property string $permissions
 * @property string $blocked_countries
 * @property string $two_factor_auth
 * @property string $notify_live_streaming
 * @property string $notify_mentions
 * @property string|null $stripe_connect_id
 * @property int $completed_stripe_onboarding
 * @property string|null $device_token
 * @property string $telegram
 * @property string $vk
 * @property string $twitch
 * @property string $discord
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Updates[] $bookmarks
 * @property-read int|null $bookmarks_count
 * @property-read \App\Models\Categories|null $category
 * @property-read \Illuminate\Database\Eloquent\Collection|\Laravel\Passport\Client[] $clients
 * @property-read int|null $clients_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Comments[] $comments
 * @property-read int|null $comments_count
 * @property-read mixed $first_name
 * @property-read mixed $last_name
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Like[] $likes
 * @property-read int|null $likes_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Updates[] $media
 * @property-read int|null $media_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Transactions[] $myPayments
 * @property-read int|null $my_payments_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Transactions[] $myPaymentsReceived
 * @property-read int|null $my_payments_received_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Subscriptions[] $mySubscriptions
 * @property-read int|null $my_subscriptions_count
 * @property-read \Illuminate\Database\Eloquent\Collection|Notifications[] $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Updates[] $payPerView
 * @property-read int|null $pay_per_view_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Messages[] $payPerViewMessages
 * @property-read int|null $pay_per_view_messages_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Plans[] $plans
 * @property-read int|null $plans_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Products[] $products
 * @property-read int|null $products_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Purchases[] $purchasedItems
 * @property-read int|null $purchased_items_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\ReferralTransactions[] $referralTransactions
 * @property-read int|null $referral_transactions_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Referrals[] $referrals
 * @property-read int|null $referrals_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Restrictions[] $restrictions
 * @property-read int|null $restrictions_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Purchases[] $sales
 * @property-read int|null $sales_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\Laravel\Cashier\Subscription[] $subscriptions
 * @property-read int|null $subscriptions_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\Laravel\Passport\Token[] $tokens
 * @property-read int|null $tokens_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Updates[] $updates
 * @property-read int|null $updates_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Subscriptions[] $userSubscriptions
 * @property-read int|null $user_subscriptions_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Withdrawals[] $withdrawals
 * @property-read int|null $withdrawals_count
 * @method static \Illuminate\Database\Eloquent\Builder|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User query()
 * @method static \Illuminate\Database\Eloquent\Builder|User whereAbout($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereActiveStatusOnline($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereAllowDownloadFiles($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereAvatar($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereBalance($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereBank($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereBirthdate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereBirthdateChanged($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereBlockedCountries($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereCategoriesId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereCity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereCompany($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereCompletedStripeOnboarding($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereConfirmationCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereCountriesId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereCover($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereCustomFee($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereDarkMode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereDeviceToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereDiscord($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereEmailNewPpv($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereEmailNewSubscriber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereEmailNewTip($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereFacebook($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereFeatured($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereFeaturedDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereFreeSubscription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereGender($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereGithub($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereHideCountSubscribers($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereHideLastSeen($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereHideMyCountry($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereHideName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereHideProfile($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereInstagram($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereIp($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereLanguage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereLastLogin($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereLastSeen($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereNotifyCommentedPost($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereNotifyEmailNewPost($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereNotifyLikedComment($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereNotifyLikedPost($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereNotifyLiveStreaming($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereNotifyMentions($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereNotifyNewPost($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereNotifyNewPpv($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereNotifyNewSubscriber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereNotifyNewTip($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereOauthProvider($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereOauthUid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User wherePaymentGateway($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User wherePayoneerAccount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User wherePaypalAccount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User wherePaystackAuthorizationCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User wherePaystackCardBrand($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User wherePaystackExp($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User wherePaystackLast4($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User wherePaystackPlan($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User wherePermission($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User wherePermissions($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User wherePinterest($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User wherePlan($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User wherePmLastFour($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User wherePmType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User wherePostLocked($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereProfession($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereRole($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereShowMyBirthdate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereSnapchat($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereStory($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereStripeConnectId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereStripeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereTelegram($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereTiktok($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereTrialEndsAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereTwitch($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereTwitter($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereTwoFactorAuth($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereUsername($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereVerifiedId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereVk($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereWallet($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereWebsite($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereYoutube($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereZelleAccount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereZip($value)
 * @mixin \Eloquent
 */
class User extends Authenticatable implements HasLocalePreference
{
    use Notifiable, Billable, HasApiTokens;

    const CREATED_AT = 'date';
  	const UPDATED_AT = null;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'username',
        'countries_id',
        'name',
        'email',
        'password',
        'avatar',
        'cover',
        'status',
        'role',
        'permission',
        'confirmation_code',
        'oauth_uid',
        'oauth_provider',
        'token',
        'story',
        'verified_id',
        'ip',
        'language',
        'free_subscription',
        'stripe_connect_id',
        'completed_stripe_onboarding',
        'device_token'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The tax rates that should apply to the customer's subscriptions.
     *
     * @return array
     */
    public function taxRates()
    {
      $taxRates = [];
      $payment = PaymentGateways::whereName('Stripe')
  			->whereEnabled('1')
  			->where('key_secret', '<>', '')
  			->first();

        if ($payment) {
          $stripe = new \Stripe\StripeClient($payment->key_secret);
          $taxes = $stripe->taxRates->all();

          foreach ($taxes->data as $tax) {
            if ($tax->active && $tax->state == $this->getRegion()
                && $tax->country == $this->getCountry()
                || $tax->active
                && $tax->country == $this->getCountry()
                && $tax->state == null
              ) {
               $taxRates[] = $tax->id;
            }
          }
        }

      return $taxRates;
    }

    public function isTaxable()
    {
      return TaxRates::whereStatus('1')
      ->whereIsoState($this->getRegion())
      ->whereCountry($this->getCountry())
        ->orWhere('country', $this->getCountry())
        ->whereNull('iso_state')
        ->whereStatus('1')
      ->get();
    }

    public function taxesPayable()
    {
      return $this->isTaxable()
          ->pluck('id')
          ->implode('_');
    }

    public function getCountry()
    {
       $ip = request()->ip();
       return cache('userCountry-'.$ip) ?? ($this->country()->country_code ?? null);
    }

    public function getRegion()
    {
       $ip = request()->ip();
       return cache('userRegion-'.$ip);
    }


    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPasswordNotification($token));
    }

    public function userSubscriptions()
    {
      return $this->hasMany(Subscriptions::class);
    }

    public function mySubscriptions()
    {
          return $this->hasManyThrough(
                Subscriptions::class,
                Plans::class,
                'user_id',
                'stripe_price',
                'id',
                'name'
              );
      }

    public function myPayments()
    {
          return $this->hasMany(Transactions::class);
      }

    public function myPaymentsReceived()
    {
          return $this->hasMany(Transactions::class, 'subscribed')->where('approved', '<>', '0');
      }

    public function updates()
    {
      return $this->hasMany(Updates::class)->where('status', 'active');
    }

    public function media()
    {
      return $this->belongsToMany(Updates::class,
          'media',
          'user_id',
          'updates_id')
          ->where('updates.status', 'active')
          ->where('media.status', 'active');
    }

      public function withdrawals()
      {
        return $this->hasMany(Withdrawals::class);
    }

  	public function country()
    {
          return $this->belongsTo(Countries::class, 'countries_id')->first();
      }

      public function notifications()
      {
            return $this->hasMany(Notifications::class, 'destination');
        }

      public function messagesInbox()
      {
            return $this->hasMany(Messages::class, 'to_user_id')->where('status','new')->count();
        }

      public function comments()
      {
            return $this->hasMany(Comments::class);
        }

      public function likes()
      {
        return $this->hasMany(Like::class);
      }

      public function category()
      {
        return $this->belongsTo(Categories::class, 'categories_id');
      }

      public function verificationRequests()
      {
            return $this->hasMany(VerificationRequests::class)->whereStatus('pending')->count();
        }

      public static function notificationsCount()
      {
        // Notifications Count
      	$notifications_count = auth()->user()->notifications()->where('status', '0')->count();
        // Messages
      	$messages_count = auth()->user()->messagesInbox();

        if ($messages_count != 0 &&  $notifications_count != 0) {
          $totalNotifications = ($messages_count + $notifications_count);
        } elseif ($messages_count == 0 && $notifications_count != 0) {
          $totalNotifications = $notifications_count;
        } elseif ($messages_count != 0 && $notifications_count == 0) {
          $totalNotifications = $messages_count;
        } else {
          $totalNotifications = null;
        }

       return $totalNotifications;
    }

      function getFirstNameAttribute()
      {
        $name = explode(' ', $this->name);
        return $name[0] ?? null;
      }

      function getLastNameAttribute()
      {
        $name = explode(' ', $this->name);
        return $name[1] ?? null;
      }

      public function bookmarks()
      {
        return $this->belongsToMany(Updates::class, 'bookmarks','user_id','updates_id');
      }

      public function likesCount()
      {
        return $this->hasManyThrough(Like::class, Updates::class, 'user_id', 'updates_id')->where('likes.status', '=', '1')->count();
      }

      public function checkSubscription($user)
      {
        return $this->userSubscriptions()
            ->whereIn('stripe_price', $user->plans()->pluck('name'))
            ->where('stripe_id', '=', '')
            ->where('ends_at', '>=', now())

              ->orWhere('stripe_status', 'active')
                ->whereIn('stripe_price', $user->plans()->pluck('name'))
              ->where('stripe_id', '<>', '')
              ->whereUserId($this->id)

              ->orWhere('stripe_id', '<>', '')
                ->whereIn('stripe_price', $user->plans()->pluck('name'))
                ->where('stripe_status', 'canceled')
                ->where('ends_at', '>=', now())
              ->whereUserId($this->id)

              ->orWhere('stripe_id', '=', '')
                ->where('stripe_price', $user->plan)
              ->whereFree('yes')
              ->whereUserId($this->id)
              ->first();
            }

        public function subscriptionsActive()
        {
          return $this->mySubscriptions()
              ->where('stripe_id', '=', '')
                ->where('ends_at', '>=', now())
                ->orWhere('stripe_status', 'active')
                  ->where('stripe_id', '<>', '')
                    ->whereIn('stripe_price', $this->plans()->pluck('name'))
                    ->orWhere('stripe_id', '=', '')
                  ->where('stripe_price', $this->plan)
              ->where('free', '=', 'yes')
            ->first();
        }

        public function totalSubscriptionsActive()
        {
          return $this->mySubscriptions()
              ->where('stripe_id', '=', '')
                ->where('ends_at', '>=', now())
                ->orWhere('stripe_status', 'active')
                  ->where('stripe_id', '<>', '')
                    ->whereIn('stripe_price', $this->plans()->pluck('name'))
                    ->orWhere('stripe_id', '=', '')
                  ->where('stripe_price', $this->plan)
              ->where('free', '=', 'yes')
            ->count();
        }

        public function payPerView()
        {
          return $this->belongsToMany(Updates::class, 'pay_per_views','user_id','updates_id');
        }


        public function payPerViewMessages()
        {
          return $this->belongsToMany(Messages::class, 'pay_per_views','user_id','messages_id');
        }

    /**
     * Get the user's preferred locale.
     */
    public function preferredLocale()
    {
        return $this->language;
    }

    /**
     * Get the user's is Super Admin.
     */
    public function isSuperAdmin()
    {
      if ($this->permissions == 'full_access') {
        return $this->id;
      }
        return false;
    }

    /**
     * Get the user's permissions.
     */
    public function hasPermission($section)
    {
      $permissions = explode(',', $this->permissions);

      return in_array($section, $permissions)
            || $this->permissions == 'full_access'
            || $this->permissions == 'limited_access'
            ? true
            : false;
    }

    /**
     * Get the user's blocked countries.
     */
    public function blockedCountries()
    {
      return explode(',', $this->blocked_countries);
    }

    /**
     * Get Referrals.
     */
    public function referrals()
    {
      return $this->hasMany(Referrals::class, 'referred_by');
    }

    public function referralTransactions() {
      return $this->hasMany(ReferralTransactions::class, 'referred_by');
    }

    /**
     * Broadcasting Live
     */
     public function isLive()
     {
       return $this->hasMany(LiveStreamings::class)
         ->where('updated_at', '>', now()->subMinutes(5))
         ->whereStatus('0')
         ->orderBy('id', 'desc')
         ->first();
     }

     /**
      * User plans
      */
      public function plans()
      {
        return $this->hasMany(Plans::class);
      }

      // Get details plan
      public function plan($interval, $field)
      {
        return $this->plans()
            ->whereInterval($interval)
            ->pluck($field)
            ->first();
      }

      // Set interval subscriptions
      public function planInterval($interval)
      {
        switch ($interval) {
          case 'weekly':
            return now()->add(7, 'days');
            break;

          case 'monthly':
            return now()->add(1, 'month');
            break;

          case 'quarterly':
            return now()->add(3, 'months');
            break;

          case 'biannually':
            return now()->add(6, 'months');
            break;

          case 'yearly':
            return now()->add(12, 'months');
            break;
        }
      }

      // Get Plan Active
      public function planActive()
      {
        return $this->plans()->whereStatus('1')->first();
      }

      public function purchasedItems()
      {
        return $this->hasMany(Purchases::class);
      }

      public function products()
      {
        return $this->hasMany(Products::class);
      }

      public function sales()
  		{
  			return $this->belongsToMany(
  						Purchases::class,
  						Products::class,
  						'user_id',
  						'id',
  						'id',
  						'products_id'
  					);
  		}

      public function restrictions()
      {
        return $this->hasMany(Restrictions::class);
      }

      public function isRestricted($user)
      {
        return Restrictions::whereUserId($this->id)
          ->whereUserRestricted($user)
          ->first();
      }

      public function checkRestriction($user)
      {
        return Restrictions::whereUserId($this->id)
          ->whereUserRestricted($user)
          ->orWhere('user_id', $user)
          ->whereUserRestricted($this->id)
          ->first();
      }

}
