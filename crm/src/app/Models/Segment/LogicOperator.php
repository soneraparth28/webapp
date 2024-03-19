<?php


namespace App\Models\Segment;


use App\Models\AppModel;
use App\Models\Core\Traits\Translate\TranslatedNameTrait;

/**
 * App\Models\Segment\LogicOperator
 *
 * @property int $id
 * @property string $name
 * @property string $type
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Segment\LogicName[] $fieldName
 * @property-read int|null $field_name_count
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Core\BaseModel filters(\App\Filters\FilterBuilder $filter)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Segment\LogicOperator newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Segment\LogicOperator newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Segment\LogicOperator query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Segment\LogicOperator whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Segment\LogicOperator whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Segment\LogicOperator whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Segment\LogicOperator whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Segment\LogicOperator whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class LogicOperator extends AppModel
{
    protected $appends = ['translated_name'];
    use TranslatedNameTrait;

    protected $fillable = [
        'name', 'type'
    ];

    public function fieldName()
    {
        return $this->belongsToMany(
            LogicName::class,
            'logic_relation',
            'operator_id',
            'name_id'
        );
    }
}
