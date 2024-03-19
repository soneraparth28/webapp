<?php

namespace App\Models\Template;

use App\Models\Core\Auth\User;
use App\Models\Core\Builder\Template\Template as BaseTemplate;
use App\Models\Core\File\File;
use App\Models\Core\Traits\BootTrait;
use App\Models\Core\Traits\BrandRelationship;
use App\Models\Core\Traits\CreatedByRelationship;

/**
 * App\Models\Template\Template
 *
 * @property int $id
 * @property string|null $subject
 * @property string|null $default_content
 * @property string|null $custom_content
 * @property int|null $brand_id
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Core\App\Brand\Brand|null $brand
 * @property-read \App\Models\Core\Auth\User|null $createdBy
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Core\BaseModel filters(\App\Filters\FilterBuilder $filter)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Template\Template newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Template\Template newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Template\Template query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Template\Template whereBrandId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Template\Template whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Template\Template whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Template\Template whereCustomContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Template\Template whereDefaultContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Template\Template whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Template\Template whereSubject($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Template\Template whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Template\Template whereUpdatedBy($value)
 * @mixin \Eloquent
 */
class Template extends BaseTemplate
{
    protected $fillable = [
        'subject', 'custom_content', 'brand_id', 'created_by', 'updated_by'
    ];

    protected static $logAttributes = [
        'subject', 'brand.name', 'createdBy.full_name', 'updatedBy.full_name'
    ];

    use BootTrait {
        boot as public traitBoot;
    }

    use BrandRelationship,
        CreatedByRelationship;


    public function thumbnail()
    {
        return $this->morphOne(File::class, 'fileable')
            ->where('type', 'template');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by', 'id');
    }

    public function createdRules()
    {
        return [
            'subject' => 'required',
            'custom_content' => 'required|min:10'
        ];
    }
    public static function boot()
    {
        self::traitBoot();
    }

}
