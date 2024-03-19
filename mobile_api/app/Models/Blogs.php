<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Blogs
 *
 * @property int $id
 * @property string $title
 * @property string $slug
 * @property string $image
 * @property string $content
 * @property string $tags
 * @property int $user_id
 * @property \Illuminate\Support\Carbon $date
 * @method static \Illuminate\Database\Eloquent\Builder|Blogs newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Blogs newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Blogs query()
 * @method static \Illuminate\Database\Eloquent\Builder|Blogs whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Blogs whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Blogs whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Blogs whereImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Blogs whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Blogs whereTags($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Blogs whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Blogs whereUserId($value)
 * @mixin \Eloquent
 */
class Blogs extends Model
{
  protected $guarded = array();
  const CREATED_AT = 'date';
	const UPDATED_AT = null;

  public function user() {
        return $this->belongsTo('App\Models\User')->first();
    }

}
