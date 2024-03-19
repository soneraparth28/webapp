<?php

namespace App\Models\Core\Builder\Template;

use App\Models\AppModel;
use App\Models\Core\Builder\Template\Traits\TemplateRelationShip;
use App\Models\Core\Traits\BootTrait;
use App\Models\Core\Traits\DescriptionGeneratorTrait;
use Mews\Purifier\Casts\CleanHtmlInput;

/**
 * App\Models\Core\Builder\Template\Template
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
 * @property-read \Illuminate\Database\Eloquent\Collection|\Spatie\Activitylog\Models\Activity[] $activities
 * @property-read int|null $activities_count
 * @property-read \App\Models\Core\App\Brand\Brand|null $brand
 * @property-read \App\Models\Core\Auth\User|null $createdBy
 * @property-read \App\Models\Core\Auth\User|null $updatedBy
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Core\BaseModel filters(\App\Filters\FilterBuilder $filter)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Core\Builder\Template\Template newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Core\Builder\Template\Template newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Core\Builder\Template\Template query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Core\Builder\Template\Template whereBrandId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Core\Builder\Template\Template whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Core\Builder\Template\Template whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Core\Builder\Template\Template whereCustomContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Core\Builder\Template\Template whereDefaultContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Core\Builder\Template\Template whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Core\Builder\Template\Template whereSubject($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Core\Builder\Template\Template whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Core\Builder\Template\Template whereUpdatedBy($value)
 * @mixin \Eloquent
 */
class Template extends AppModel
{
    use TemplateRelationShip, BootTrait, DescriptionGeneratorTrait;

    protected static $logAttributes = [
        'subject', 'brand.name'
    ];

//    protected $casts = [
//        'custom_content' => CleanHtmlInput::class, // cleans when setting the value
//    ];

    protected $fillable = ['subject', 'custom_content', 'brand_id', 'created_by', 'updated_by'];

    public function createdRules()
    {
        return [
            'custom_content' => 'required|min:10',
            'subject' => 'required'
        ];
    }
}
