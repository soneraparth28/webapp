<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\TaxRates
 *
 * @property int $id
 * @property string $name
 * @property int $type
 * @property string $percentage
 * @property string|null $country
 * @property string|null $state
 * @property string|null $iso_state
 * @property string|null $stripe_id
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|TaxRates newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TaxRates newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TaxRates query()
 * @method static \Illuminate\Database\Eloquent\Builder|TaxRates whereCountry($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TaxRates whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TaxRates whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TaxRates whereIsoState($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TaxRates whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TaxRates wherePercentage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TaxRates whereState($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TaxRates whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TaxRates whereStripeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TaxRates whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TaxRates whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class TaxRates extends Model
{
    use HasFactory;

    public function country()
  	{
  		return $this->belongsTo(Countries::class, 'country', 'country_code')->first();
  	}

    public function state()
  	{
  		return $this->belongsTo(States::class, 'iso_state', 'code')->first();
  	}
}
