<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\States
 *
 * @property int $id
 * @property int $countries_id
 * @property string $name
 * @property string $code
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|States newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|States newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|States query()
 * @method static \Illuminate\Database\Eloquent\Builder|States whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|States whereCountriesId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|States whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|States whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|States whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|States whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class States extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function country()
  	{
  		return $this->belongsTo(Countries::class, 'countries_id')->first();
  	}
}
