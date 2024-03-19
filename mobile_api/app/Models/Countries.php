<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Countries
 *
 * @property int $id
 * @property string $country_code
 * @property string $country_name
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\States[] $states
 * @property-read int|null $states_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\User[] $users
 * @property-read int|null $users_count
 * @method static \Illuminate\Database\Eloquent\Builder|Countries newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Countries newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Countries query()
 * @method static \Illuminate\Database\Eloquent\Builder|Countries whereCountryCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Countries whereCountryName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Countries whereId($value)
 * @mixin \Eloquent
 */
class Countries extends Model {

	protected $guarded = [];
	public $timestamps = false;

	public function users()
	{
		return $this->hasMany(User::class);
	}

	public function states()
	{
		return $this->hasMany(States::class);
	}

}
