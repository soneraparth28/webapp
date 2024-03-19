<?php

namespace App\Models; 

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Languages
 *
 * @property int $id
 * @property string $name
 * @property string $abbreviation
 * @method static \Illuminate\Database\Eloquent\Builder|Languages newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Languages newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Languages query()
 * @method static \Illuminate\Database\Eloquent\Builder|Languages whereAbbreviation($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Languages whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Languages whereName($value)
 * @mixin \Eloquent
 */
class Languages extends Model {

	protected $guarded = array();
	public $timestamps = false;

	protected $fillable = [ 'name', 'abbreviation' ];
}
