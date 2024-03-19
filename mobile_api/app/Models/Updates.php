<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Updates
 *
 * @property int $id
 * @property string $image
 * @property string $video
 * @property string $description
 * @property int $user_id
 * @property string $date
 * @property string $token_id
 * @property string $locked
 * @property string $music
 * @property string $file
 * @property string $img_type
 * @property string $fixed_post
 * @property string $price
 * @property string $video_embed
 * @property string $file_name
 * @property string $file_size
 * @property string $status
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\User[] $bookmarks
 * @property-read int|null $bookmarks_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Comments[] $comments
 * @property-read int|null $comments_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Like[] $likes
 * @property-read int|null $likes_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Media[] $media
 * @property-read int|null $media_count
 * @method static \Illuminate\Database\Eloquent\Builder|Updates newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Updates newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Updates query()
 * @method static \Illuminate\Database\Eloquent\Builder|Updates whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Updates whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Updates whereFile($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Updates whereFileName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Updates whereFileSize($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Updates whereFixedPost($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Updates whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Updates whereImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Updates whereImgType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Updates whereLocked($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Updates whereMusic($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Updates wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Updates whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Updates whereTokenId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Updates whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Updates whereVideo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Updates whereVideoEmbed($value)
 * @mixin \Eloquent
 */
class Updates extends Model {

	protected $guarded = array();
	public $timestamps = false;

	public function user() {
		return $this->belongsTo('App\Models\User')->first();
	}

	public function media() {
		return $this->hasMany('App\Models\Media')->where('status', 'active')->orderBy('id','asc');
	}

	public function likes() {
		return $this->hasMany('App\Models\Like')->where('status', '1');
	}

	 public function comments() {
		return $this->hasMany('App\Models\Comments');
	}

	public function bookmarks() {
		return $this->belongsToMany('App\Models\User', 'bookmarks','updates_id','user_id');
	}

}
