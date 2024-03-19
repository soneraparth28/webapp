<?php


namespace App\Repositories\Lists;


use App\Models\Lists\Lists;
use App\Models\Segment\Segment;
use App\Repositories\App\AppRepository;
use App\Repositories\App\StatusRepository;
use Illuminate\Support\Collection;

class ListRepository extends AppRepository
{
    public function __construct(Lists $list)
    {
        $this->model = $list;
    }

    public function gatherSubscribers($withIds = [])
    {
        return $this->model->segments->map(function (Segment $segment){
            return $segment->subscribersEssentialFieldOnly();
        })->flatten()
            ->unique('email')
            ->whereNotIn('id', $withIds)
            ->pluck('id')
            ->merge($withIds)
            ->toArray();
    }

    public function segmentSubscribers(Collection $subscribers)
    {
        return $this->model->segments->map(function (Segment $segment) {
            return $segment->subscribers();
        })->flatten()
            ->unique('email')
            ->whereNotIn('id', $subscribers->pluck('id')->toArray())
            ->merge($subscribers)
            ->values();
    }

    public function segmentOnlySubscribers()
    {
        return $this->model->segments->map(function (Segment $segment) {
            return $segment->subscribersEssentialFieldOnly();
        })->flatten()
            ->unique('email')
            ->values();
    }

    public function subscribersWithEssentialField(Collection $subscribers)
    {
        $statuses = resolve(StatusRepository::class)
            ->subscriber('status_subscribed');

        return $this->model->segments->map(function (Segment $segment) use ($statuses) {
            return $segment->subscribersEssentialFieldOnly(array_keys($statuses));
        })->flatten()
            ->merge($subscribers)
            ->unique('email')
            ->values();
    }
}
