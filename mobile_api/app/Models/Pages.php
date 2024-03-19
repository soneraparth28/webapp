<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Pages
 *
 * @property int $id
 * @property string $title
 * @property string|null $content
 * @property string $slug
 * @property string $description
 * @property string $keywords
 * @property string $lang
 * @property string $access
 * @method static \Illuminate\Database\Eloquent\Builder|Pages newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Pages newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Pages query()
 * @method static \Illuminate\Database\Eloquent\Builder|Pages whereAccess($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Pages whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Pages whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Pages whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Pages whereKeywords($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Pages whereLang($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Pages whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Pages whereTitle($value)
 * @mixin \Eloquent
 */
class Pages extends Model {

	protected $guarded = array();
	public $timestamps = false;
}
