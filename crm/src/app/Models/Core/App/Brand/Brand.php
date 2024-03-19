<?php

namespace App\Models\Core\App\Brand;

use App\Models\Core\App\Brand\Traits\BrandMethod;
use App\Models\Core\App\Brand\Traits\BrandRelationship;
use App\Models\Core\App\Brand\Traits\BrandRules;
use App\Models\Core\BaseModel;
use App\Models\Core\Traits\BootTrait;
use App\Models\Core\Traits\DescriptionGeneratorTrait;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * App\Models\Core\App\Brand\Brand
 *
 * @property int $id
 * @property string $name
 * @property string $short_name
 * @property int $created_by
 * @property int|null $status_id
 * @property int|null $brand_group_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\Spatie\Activitylog\Models\Activity[] $activities
 * @property-read int|null $activities_count
 * @property-read \App\Models\Core\Auth\User $createdBy
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Core\Builder\Form\CustomFieldValue[] $customFields
 * @property-read int|null $custom_fields_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Core\Setting\Setting[] $settings
 * @property-read int|null $settings_count
 * @property-read \App\Models\Core\Status|null $status
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Core\Auth\User[] $users
 * @property-read int|null $users_count
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Core\BaseModel filters(\App\Filters\FilterBuilder $filter)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Core\App\Brand\Brand newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Core\App\Brand\Brand newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Core\App\Brand\Brand query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Core\App\Brand\Brand whereBrandGroupId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Core\App\Brand\Brand whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Core\App\Brand\Brand whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Core\App\Brand\Brand whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Core\App\Brand\Brand whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Core\App\Brand\Brand whereShortName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Core\App\Brand\Brand whereStatusId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Core\App\Brand\Brand whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Brand extends BaseModel
{
    use BootTrait {
        boot as public traitBoot;
    }
    use BrandRelationship, BrandRules, BrandMethod, DescriptionGeneratorTrait;

    protected static $logAttributes = [
        'name'
    ];

    protected $fillable = [
        'name', 'short_name', 'created_by', 'status_id', 'brand_group_id'
    ];

    public static function boot()
    {
        self::traitBoot();
    }

}
