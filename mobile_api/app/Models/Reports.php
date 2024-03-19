<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Reports
 *
 * @property int $id
 * @property int $user_id
 * @property int $report_id
 * @property string $type
 * @property string $reason
 * @property \Illuminate\Support\Carbon $created_at
 * @method static \Illuminate\Database\Eloquent\Builder|Reports newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Reports newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Reports query()
 * @method static \Illuminate\Database\Eloquent\Builder|Reports whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Reports whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Reports whereReason($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Reports whereReportId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Reports whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Reports whereUserId($value)
 * @mixin \Eloquent
 */
class Reports extends Model
{

  protected $guarded = array();
	const UPDATED_AT = null;

  public function user(){
    return $this->belongsTo('App\Models\User')->first();
  }

  public function userReported(){
    return $this->belongsTo('App\Models\User', 'report_id')->first();
  }

   public function updates(){
    return $this->belongsTo('App\Models\Updates', 'report_id')->first();
  }
}
