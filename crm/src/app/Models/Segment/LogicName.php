<?php


namespace App\Models\Segment;


use App\Models\AppModel;
use App\Models\Core\Traits\Translate\TranslatedNameTrait;

/**
 * App\Models\Segment\LogicName
 *
 * @property int $id
 * @property string $name
 * @property string $type
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Segment\LogicOperator[] $operator
 * @property-read int|null $operator_count
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Core\BaseModel filters(\App\Filters\FilterBuilder $filter)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Segment\LogicName newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Segment\LogicName newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Segment\LogicName query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Segment\LogicName whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Segment\LogicName whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Segment\LogicName whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Segment\LogicName whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Segment\LogicName whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class LogicName extends AppModel
{
    protected $appends = ['translated_name'];
    use TranslatedNameTrait;

    protected $fillable = [
        'name', 'type'
    ];

    public function operator()
    {
        return $this->belongsToMany(
            LogicOperator::class,
            'logic_relation',
            'name_id',
            'operator_id'
        );
    }
}
