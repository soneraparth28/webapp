<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\PayPerViews
 *
 * @property int $id
 * @property int $user_id
 * @property int $updates_id
 * @property int $messages_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|PayPerViews newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PayPerViews newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PayPerViews query()
 * @method static \Illuminate\Database\Eloquent\Builder|PayPerViews whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PayPerViews whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PayPerViews whereMessagesId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PayPerViews whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PayPerViews whereUpdatesId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PayPerViews whereUserId($value)
 * @mixin \Eloquent
 */
class PayPerViews extends Model {

	protected $guarded = array();
		
}
