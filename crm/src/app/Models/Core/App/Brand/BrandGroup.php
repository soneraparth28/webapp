<?php

namespace App\Models\Core\App\Brand;

use App\Models\Core\App\Brand\Traits\BrandGroupRelationship as Relationship;
use App\Models\Core\App\Brand\Traits\BrandGroupRules;
use App\Models\Core\BaseModel;
use App\Models\Core\Traits\BootTrait;
use App\Models\Core\Traits\DescriptionGeneratorTrait;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * App\Models\Core\App\Brand\BrandGroup
 *
 * @property int $id
 * @property string $name
 * @property int|null $created_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\Spatie\Activitylog\Models\Activity[] $activities
 * @property-read int|null $activities_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Core\App\Brand\Brand[] $brands
 * @property-read int|null $brands_count
 * @property-read \App\Models\Core\Auth\User|null $createdBy
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Core\BaseModel filters(\App\Filters\FilterBuilder $filter)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Core\App\Brand\BrandGroup newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Core\App\Brand\BrandGroup newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Core\App\Brand\BrandGroup query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Core\App\Brand\BrandGroup whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Core\App\Brand\BrandGroup whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Core\App\Brand\BrandGroup whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Core\App\Brand\BrandGroup whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Core\App\Brand\BrandGroup whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class BrandGroup extends BaseModel
{
    use BootTrait, Relationship, BrandGroupRules, DescriptionGeneratorTrait;
    protected $table = 'brand_groups';

    protected static $logAttributes = [
        'name'
    ];

    protected $fillable = ['name', 'created_by'];



}
