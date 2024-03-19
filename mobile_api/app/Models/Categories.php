<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Categories
 *
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property string $description
 * @property string $keywords
 * @property string $mode
 * @property string $image
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\User[] $users
 * @property-read int|null $users_count
 * @method static \Illuminate\Database\Eloquent\Builder|Categories newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Categories newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Categories query()
 * @method static \Illuminate\Database\Eloquent\Builder|Categories whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Categories whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Categories whereImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Categories whereKeywords($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Categories whereMode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Categories whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Categories whereSlug($value)
 * @mixin \Eloquent
 */
class Categories extends Model {

	protected $guarded = array();
	public $timestamps = false;

	public function users() {
		return $this->hasMany('App\Models\User')->where('status','active');
	}
}
