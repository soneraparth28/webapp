<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Comments
 *
 * @property int $id
 * @property int $updates_id
 * @property int $user_id
 * @property string $reply
 * @property \Illuminate\Support\Carbon $date
 * @property string $status 0 Trash, 1 Active
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\CommentsLikes[] $likes
 * @property-read int|null $likes_count
 * @method static \Illuminate\Database\Eloquent\Builder|Comments newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Comments newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Comments query()
 * @method static \Illuminate\Database\Eloquent\Builder|Comments whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Comments whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Comments whereReply($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Comments whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Comments whereUpdatesId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Comments whereUserId($value)
 * @mixin \Eloquent
 */
class Comments extends Model
{
	protected $guarded = array();
	const CREATED_AT = 'date';
	const UPDATED_AT = null;

	public function user()
	{
		return $this->belongsTo('App\Models\User')->first();
	}

	public function updates()
	{
		return $this->belongsTo('App\Models\Updates')->first();
	}

	public function likes()
	{
		return $this->hasMany('App\Models\CommentsLikes');
	}

}
