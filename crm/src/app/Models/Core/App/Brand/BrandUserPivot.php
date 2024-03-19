<?php

namespace App\Models\Core\App\Brand;

use App\Models\Core\App\Brand\Traits\BrandUserBootTrait;
use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * App\Models\Core\App\Brand\BrandUserPivot
 *
 * @property int $brand_id
 * @property int $user_id
 * @property \Illuminate\Support\Carbon $assigned_at
 * @property int $assigned_by
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Core\App\Brand\BrandUserPivot newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Core\App\Brand\BrandUserPivot newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Core\App\Brand\BrandUserPivot query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Core\App\Brand\BrandUserPivot whereAssignedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Core\App\Brand\BrandUserPivot whereAssignedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Core\App\Brand\BrandUserPivot whereBrandId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Core\App\Brand\BrandUserPivot whereUserId($value)
 * @mixin \Eloquent
 */
class BrandUserPivot extends Pivot
{
    use BrandUserBootTrait;
    protected $table = 'brand_user';

    protected $dates = [
        'assigned_at'
    ];
}
