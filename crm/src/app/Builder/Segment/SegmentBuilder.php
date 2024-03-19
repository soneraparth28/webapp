<?php


namespace App\Builder\Segment;


use App\Builder\Segment\Traits\SegmentOperator;
use App\Models\Segment\Segment;
use App\Models\Subscriber\Subscriber;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SegmentBuilder
{
    use SegmentOperator;
    protected $segment;
    protected $query;

    protected $betweenMethod = ['orWhereBetween', 'whereBetween'];

    protected $custom = null;

    public function __construct(Segment $segment)
    {
        $this->segment = $segment;
        $this->query = Subscriber::where('brand_id', $this->segment->brand_id);
    }

    /**
     * @return Builder
     */
    public function initialize()
    {
        $segment_logic = $this->segment->segment_logic;

        foreach ($segment_logic as $key => $logic) {
            $this->query->where(function (Builder $builder) use ($logic) {
                foreach ($logic as $k => $l) {

                    [$method, $type] = $this->whereMethodParser($l, $k);

                    [$column, $operator, $value] = $parameters = $this->whereParameterParser($l);

                    //if the query is on custom fields
                    if ($value == 'custom_query') {

                        $builder->whereHas($column, $operator);

                    }else if (in_array($method, $this->betweenMethod)){

                        $builder->{$method}(DB::raw($column), [$operator, $value]);

                    } else {
                        /**
                         * Type is a boolean value.
                         * As the between method is already executed in above line.
                         * So in next line for type being true we will execute whereRaw/orWhereRaw builder method
                         * For type being false we will execute where/orWhere
                         */
                        if ($type) {
                            $builder->{$method}("$column $operator ?", $value);
                        } else {
                            $builder->{$method}($column, $operator, $value);
                        }

                    }

                }
            });
        }

        return $this->query;
    }

    public function whereMethodParser($logic, $index)
    {
        if ($this->operatorsType[$logic[1]] == 'date') {
            return [
                $index ? 'orWhereRaw' : 'whereRaw',
                true
            ];
        }

        if ($this->operatorsType[$logic[1]] == 'date_range') {
            return [
                $index ? 'orWhereBetween' : 'whereBetween',
                true
            ];
        }

        return [
            $index ? 'orWhere' : 'where',
            false
        ];

    }

    public function whereParameterParser($logic)
    {
        $method = Str::camel($logic[0]);
        if (method_exists($this, $method))
            return $this->$method($logic);

        return $this->customField($logic);


    }

    public function firstName($logic)
    {
        [$operator, $value] = $this->operatorValueParser($logic);

        return ['first_name', $operator, $value];
    }

    public function lastName($logic)
    {
        [$operator, $value] = $this->operatorValueParser($logic);

        return ['last_name', $operator, $value ];
    }

    public function email($logic)
    {
        [$operator, $value] = $this->operatorValueParser($logic);

        return ['email', $operator, $value ];
    }

    public function createdAt($logic)
    {
        [$operator, $value] = $this->operatorValueParser($logic);

        return ["date(created_at)", $operator, $value ];
    }

    public function lastUpdated($logic)
    {
        [$operator, $value] = $this->operatorValueParser($logic);

        return ['date(updated_at)', $operator, $value ];
    }

    public function customField($logic)
    {
        [$operator, $value] = $this->operatorValueParser($logic);
        $field = 'customFields';

        $callBack = function (Builder $query) use ($logic, $operator, $value) {
            $query->whereHas('customField', function (Builder $query) use ($logic) {
                $query->where('name', $logic[0]);
            })->where('value', $operator, $value);
        };

        return [$field, $callBack, 'custom_query'];
    }

    public function operatorValueParser($logic) : array
    {
        return (new OperatorValueBuilder())->initialize($logic);
    }
}
