<?php


namespace App\Models\Lists\Traits;

use App\Builder\Lists\SubscriberBuilder;
use App\Models\Lists\Lists;
use App\Repositories\App\StatusRepository;
use App\Repositories\Lists\ListRepository;
use Illuminate\Database\Eloquent\Builder;

trait ListMethod
{
    public function allSubscribers()
    {
        if ($this->type == 'dynamic'){
            /** @var Lists $this */
            return resolve(ListRepository::class)
                ->setModel($this)
                ->segmentSubscribers($this->subscribers);
        }
        return $this->subscribers;
    }


    public function subscribersWithEssentialField()
    {
        /** @var Lists $list */
        $list = $this->load('subscribers:id,email,first_name,last_name,status_id');

        if ($this->type == 'dynamic'){
            /** @var Lists $this */
            return resolve(ListRepository::class)
                ->setModel($this)
                ->subscribersWithEssentialField($list->subscribers);
        }
        $statuses = resolve(StatusRepository::class)
            ->subscriber('status_subscribed');

        return $list->subscribers
            ->whereIn('status_id', array_keys($statuses))
            ->values();
    }

    public function dynamicSubscribers($statuses = [], $fields = [])
    {
        $statuses = is_array($statuses) ? $statuses : func_get_args();
        return $this->subscriberBuilder($statuses, $fields)
            ->union(
                $this->subscribers()
                    ->select(count($fields) ? $fields : 'subscribers.*')
                    ->when(count($statuses), function (Builder $builder) use($statuses) {
                        $builder->whereIn('status_id', $statuses);
                    })
            );
    }

    public function subscriberBuilder($statuses = [], $fields = [])
    {
        $builder = new SubscriberBuilder($this->segments);

        $builder->select($fields);

        if (count($statuses))
            $builder->whereStatus($statuses);

        return $builder->build();
    }

}
