<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\AdminSettings;
use App\Models\User;
use App\Models\Notifications;
use Mail;

/**
 * App\Models\Subscriptions
 *
 * @property int $id
 * @property int $user_id
 * @property string $name
 * @property string $stripe_id
 * @property string $stripe_status
 * @property string|null $stripe_price
 * @property int|null $quantity
 * @property string|null $trial_ends_at
 * @property string|null $ends_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $last_payment
 * @property string $free
 * @property string $subscription_id
 * @property string $cancelled
 * @property string $rebill_wallet
 * @property string $interval
 * @property string|null $taxes
 * @method static \Illuminate\Database\Eloquent\Builder|Subscriptions newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Subscriptions newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Subscriptions query()
 * @method static \Illuminate\Database\Eloquent\Builder|Subscriptions whereCancelled($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Subscriptions whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Subscriptions whereEndsAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Subscriptions whereFree($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Subscriptions whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Subscriptions whereInterval($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Subscriptions whereLastPayment($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Subscriptions whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Subscriptions whereQuantity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Subscriptions whereRebillWallet($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Subscriptions whereStripeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Subscriptions whereStripePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Subscriptions whereStripeStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Subscriptions whereSubscriptionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Subscriptions whereTaxes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Subscriptions whereTrialEndsAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Subscriptions whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Subscriptions whereUserId($value)
 * @mixin \Eloquent
 */
class Subscriptions extends Model
{

	protected $guarded = array();

	public function user()
	{
		return $this->belongsTo(User::class)->first();

	}

		public function subscribed()
		{
			return $this->belongsToMany(
						User::class,
						Plans::class,
						'name',
						'user_id',
						'stripe_price',
						'id'
					)->first();
		}

		public static function sendEmailAndNotify($subscriber, $user)
		{
			$user = User::find($user);
			$settings = AdminSettings::first();
			$titleSite = $settings->title;
			$sender    = $settings->email_no_reply;
			$emailUser   = $user->email;
			$fullNameUser = $user->name;
			$subject = $subscriber.' '.trans('users.has_subscribed');

			if ($user->email_new_subscriber == 'yes') {
				//<------ Send Email to User ---------->>>
				Mail::send('emails.new_subscriber', [
					'body' => $subject,
					'title_site' => $titleSite,
					'fullname'   => $fullNameUser
				],
					function($message) use ($sender, $subject, $fullNameUser, $titleSite, $emailUser)
						{
					    $message->from($sender, $titleSite)
										  ->to($emailUser, $fullNameUser)
											->subject($subject.' - '.$titleSite);
						});
					//<------ End Send Email to User ---------->>>
			}

			if ($user->notify_new_subscriber == 'yes') {
				// Send Notification to User --- destination, author, type, target
				Notifications::send($user->id, auth()->user()->id, '1', $user->id);
			}
		}
}
