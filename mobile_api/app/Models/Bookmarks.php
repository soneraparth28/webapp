<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Bookmarks
 *
 * @property int $id
 * @property int $user_id
 * @property int $updates_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Bookmarks newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Bookmarks newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Bookmarks query()
 * @method static \Illuminate\Database\Eloquent\Builder|Bookmarks whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Bookmarks whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Bookmarks whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Bookmarks whereUpdatesId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Bookmarks whereUserId($value)
 * @mixin \Eloquent
 */
class Bookmarks extends Model {

	protected $guarded = array();

}
