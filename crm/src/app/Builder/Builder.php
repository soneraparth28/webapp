<?php


namespace App\Builder;


use App\Builder\Segment\SegmentBuilder;
use App\Filters\FilterBuilder;
use App\Models\Segment\Segment;
use App\Models\Subscriber\Subscriber;
use Illuminate\Database\Eloquent\Builder as EBuilder;

class Builder
{
    /**@var $query Subscriber*/
    protected $query = '';

    protected $statuses = [];

    protected $columns = '*';

    protected $with = [];

    protected $withCount = [];

    /**@var $filters FilterBuilder*/
    protected $filters = null;

    protected $orderBy = 'ASC';

    public function __construct()
    {
        $this->query = Subscriber::query()
            ->where('id', null);
    }

    public function whereStatus($statuses)
    {
        $this->statuses = is_array($statuses) ? $statuses : func_get_args();

        return $this;
    }

    public function with($with = [])
    {
        $this->with = is_array($with) ? $with : func_get_args();
        $this->query->with($this->with);

        return $this;
    }

    public function withCount($withCount = [])
    {
        $this->withCount = is_array($withCount) ? $withCount : func_get_args();
        $this->query->withCount($this->withCount);

        return $this;
    }

    public function filters($filters)
    {
        $this->filters = $filters;

        return $this;
    }

    public function select($columns = [])
    {
        if (is_array($this->columns)) {
            $columns = array_map(function ($column) {
                return "subscribers.$column";
            }, $this->columns);
        }
        $this->columns = count($columns) ? $columns : 'subscribers.*';

        $this->query->select($this->columns);

        return $this;
    }

    public function latest()
    {
        $this->orderBy = 'DESC';

        return $this;
    }

    public function segmentBuilder(Segment $segment)
    {
        return (new SegmentBuilder($segment))
            ->initialize()
            ->select($this->columns)
            ->with($this->with)
            ->withCount($this->withCount)
            ->when(count($this->statuses), function (EBuilder $builder) {
                $builder->whereIn('status_id', $this->statuses);
            })->orderBy('id', $this->orderBy)
            ->when($this->filters, function (EBuilder $builder) {
                $builder->filters($this->filters);
            });
    }
}
