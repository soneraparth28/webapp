<?php

namespace App\Models\Subscriber;

use App\Models\AppModel;
use App\Models\Core\Traits\BootTrait;
use App\Models\Core\Traits\DescriptionGeneratorTrait;
use App\Models\Subscriber\Traits\SubscriberAttribute;
use App\Models\Subscriber\Traits\SubscriberMethod;
use App\Models\Subscriber\Traits\SubscriberRelationship;
use App\Models\Subscriber\Traits\SubscriberRules;
use App\Scopes\BrandScope;
use Illuminate\Support\Facades\Artisan;

/**
 * App\Models\Subscriber\Subscriber
 *
 * @property int $id
 * @property string $first_name
 * @property string $last_name
 * @property string $email
 * @property int $status_id
 * @property int $brand_id
 * @property int|null $created_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Core\App\Brand\Brand $brand
 * @property-read \App\Models\Core\Auth\User|null $createdBy
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Core\Builder\Form\CustomFieldValue[] $customFields
 * @property-read int|null $custom_fields_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Lists\Lists[] $lists
 * @property-read int|null $lists_count
 * @property-read \App\Models\Core\Status $status
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Core\BaseModel filters(\App\Filters\FilterBuilder $filter)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Subscriber\Subscriber newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Subscriber\Subscriber newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Subscriber\Subscriber query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Subscriber\Subscriber whereBrandId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Subscriber\Subscriber whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Subscriber\Subscriber whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Subscriber\Subscriber whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Subscriber\Subscriber whereFirstName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Subscriber\Subscriber whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Subscriber\Subscriber whereLastName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Subscriber\Subscriber whereStatusId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Subscriber\Subscriber whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Subscriber extends AppModel
{
    use BootTrait {
        boot as public traitBoot;
    }
    use SubscriberRelationship,
        SubscriberAttribute,
        SubscriberRules,
        SubscriberMethod,
        DescriptionGeneratorTrait;

    protected $fillable = [
        'first_name', 'last_name', 'email', 'brand_id', 'status_id', 'created_by'
    ];

    protected $appends = [
        'full_name'
    ];

    protected static $logAttributes = [
        'first_name', 'last_name', 'email', 'brand.name', 'createdBy.full_name', 'status.name'
    ];

    public static function boot()
    {
        self::traitBoot();
        static::addGlobalScope(new BrandScope());
    }


}
