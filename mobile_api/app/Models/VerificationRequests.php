<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

  /**
 * App\Models\VerificationRequests
 *
 * @property int $id
 * @property int $user_id
 * @property string $address
 * @property string $city
 * @property string $zip
 * @property string $image
 * @property string $status
 * @property \Illuminate\Support\Carbon $created_at
 * @property string $form_w9
 * @method static \Illuminate\Database\Eloquent\Builder|VerificationRequests newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|VerificationRequests newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|VerificationRequests query()
 * @method static \Illuminate\Database\Eloquent\Builder|VerificationRequests whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VerificationRequests whereCity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VerificationRequests whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VerificationRequests whereFormW9($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VerificationRequests whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VerificationRequests whereImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VerificationRequests whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VerificationRequests whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VerificationRequests whereZip($value)
 * @mixin \Eloquent
 */
class VerificationRequests extends Model
{
  protected $guarded = array();
  const UPDATED_AT = null;

  public function user(){
    return $this->belongsTo('App\Models\User')->first();
  }
}
