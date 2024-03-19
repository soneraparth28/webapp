<?php


namespace App\Models\Lists;
use App\Models\AppModel;
use App\Models\Core\Traits\BootTrait;
use App\Models\Core\Traits\DescriptionGeneratorTrait;
use App\Models\Lists\Traits\ListBoot;
use App\Models\Lists\Traits\ListMethod;
use App\Models\Lists\Traits\ListRelationship;
use App\Models\Lists\Traits\ListRules;

/**
 * App\Models\Lists\Lists
 *
 * @property int $id
 * @property string $name
 * @property string $description
 * @property string $type
 * @property int $status_id
 * @property int $brand_id
 * @property int|null $created_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Core\App\Brand\Brand $brand
 * @property-read \App\Models\Core\Auth\User|null $createdBy
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Segment\Segment[] $segments
 * @property-read int|null $segments_count
 * @property-read \App\Models\Core\Status $status
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Subscriber\Subscriber[] $subscribers
 * @property-read int|null $subscribers_count
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Core\BaseModel filters(\App\Filters\FilterBuilder $filter)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Lists\Lists newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Lists\Lists newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Lists\Lists query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Lists\Lists whereBrandId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Lists\Lists whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Lists\Lists whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Lists\Lists whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Lists\Lists whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Lists\Lists whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Lists\Lists whereStatusId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Lists\Lists whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Lists\Lists whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Lists extends AppModel
{
    protected $table = 'lists';

    protected $fillable = [
        'name', 'description', 'type', 'brand_id', 'created_by', 'status_id'
    ];

    protected static $logAttributes = [
        'name', 'description', 'type', 'brand.name', 'createdBy.full_name', 'status.name'
    ];

    use ListRelationship,
        ListMethod,
        ListRules,
        ListBoot,
        DescriptionGeneratorTrait;

}
