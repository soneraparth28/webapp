<?php


namespace App\Models\Segment\Traits;


use App\Builder\Segment\SegmentBuilder;
use Illuminate\Database\Eloquent\Builder;

trait SegmentMethod
{
    public function subscribers()
    {
        return (new SegmentBuilder($this))
            ->initialize()
            ->get();
    }

    public function subscribersCount($statuses = [])
    {
        return (new SegmentBuilder($this))
            ->initialize()
            ->when(count($statuses), function (Builder $builder) use ($statuses) {
                $builder->whereIn('status_id', $statuses);
            })
            ->count('*');
    }

    public function subscribersEssentialFieldOnly($statuses = [])
    {
        return (new SegmentBuilder($this))
            ->initialize()
            ->when(count($statuses), function (Builder $builder) use ($statuses) {
                $builder->whereIn('status_id', $statuses);
            })
            ->select('id', 'email', 'first_name', 'last_name', 'status_id')
            ->get();
    }
}
