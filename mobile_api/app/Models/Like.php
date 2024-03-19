<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Like
 *
 * @property int $id
 * @property int $user_id
 * @property int $updates_id
 * @property string $status 0 trash, 1 active
 * @property string $date
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Updates[] $updates
 * @property-read int|null $updates_count
 * @method static \Illuminate\Database\Eloquent\Builder|Like newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Like newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Like query()
 * @method static \Illuminate\Database\Eloquent\Builder|Like whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Like whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Like whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Like whereUpdatesId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Like whereUserId($value)
 * @mixin \Eloquent
 */
class Like extends Model {

	protected $guarded = array();
	public $timestamps = false;

	public function user() {
        return $this->belongsTo('App\Models\User')->first();
    }

	public function updates() {
        return $this->hasMany('App\Models\Updates');
    }

}
