<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Notifications
 *
 * @property int $id
 * @property int $destination
 * @property int $author
 * @property int $target
 * @property int $type 1 Subscribed, 2  Like, 3 reply, 4 Like Comment
 * @property string $status 0 unseen, 1 seen
 * @property \Illuminate\Support\Carbon $created_at
 * @method static \Illuminate\Database\Eloquent\Builder|Notifications newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Notifications newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Notifications query()
 * @method static \Illuminate\Database\Eloquent\Builder|Notifications whereAuthor($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Notifications whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Notifications whereDestination($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Notifications whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Notifications whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Notifications whereTarget($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Notifications whereType($value)
 * @mixin \Eloquent
 */
class Notifications extends Model
{
	protected $guarded = [];
  const UPDATED_AT = null;

	public function user()
	{
		return $this->belongsTo(User::class)->first();
	}

	public static function send($destination, $session_id, $type, $target)
	{
		$user = User::find($destination);

		if ($type == 5 && $user->notify_new_tip == 'no'
				|| $type == 6 && $user->notify_new_ppv == 'no')
				{
					return false;
				}

				self::create([
				'destination' => $destination,
				'author' => $session_id,
				'type' => $type,
				'target' => $target
			]);
	}

}
