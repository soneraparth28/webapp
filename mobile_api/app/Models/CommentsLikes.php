<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\CommentsLikes
 *
 * @property int $id
 * @property int $user_id
 * @property int $comments_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|CommentsLikes newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CommentsLikes newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CommentsLikes query()
 * @method static \Illuminate\Database\Eloquent\Builder|CommentsLikes whereCommentsId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CommentsLikes whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CommentsLikes whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CommentsLikes whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CommentsLikes whereUserId($value)
 * @mixin \Eloquent
 */
class CommentsLikes extends Model {

	protected $guarded = array();

	public function user()
	{
		return $this->belongsTo('App\Models\User')->first();
	}


}
