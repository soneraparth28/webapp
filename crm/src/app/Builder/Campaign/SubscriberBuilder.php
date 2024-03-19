<?php


namespace App\Builder\Campaign;


use App\Builder\Builder as SBuilder;
use App\Helpers\Traits\HasMemoization;
use App\Models\Campaign\Campaign;
use App\Models\Lists\Lists;
use App\Models\Segment\Segment;
use App\Models\Subscriber\Subscriber;
use Illuminate\Database\Eloquent\Builder as EBuilder;

class SubscriberBuilder extends SBuilder
{
    use HasMemoization;

    protected $lists = [];

    /**@var $campaign Campaign*/
    protected $campaign = '';

    public function __construct(Campaign $campaign)
    {
        $this->campaign = $campaign;

        $this->lists = $this->memoize('campaign-list-segment-'.$campaign->id, function () use ($campaign) {
            return $campaign->lists()->load('segments');
        });

        parent::__construct();
    }

    public function withSubscribers()
    {
        $this->query->when(count($this->campaign->subscriberAudiences()), function (EBuilder $builder) {
            return $builder->union(
                Subscriber::select($this->columns)
                    ->when(count($this->statuses), function (EBuilder $builder) {
                        $builder->whereIn('status_id', $this->statuses);
                    })
                    ->whereIn('id', $this->campaign->subscriberAudiences())
                    ->withCount($this->withCount)
                    ->with($this->with)
                    ->orderBy('id', $this->orderBy)
                    ->when($this->filters, function (EBuilder $builder) {
                        $builder->filters($this->filters);
                    })
            );
        });

        return $this;
    }

    public function build()
    {
        $this->lists->each(function (Lists $lists) {
            if ($lists->type == 'imported'){
                $this->query->union($this->listImportedSubscriber($lists));
            }elseif ($lists->type == 'dynamic') {
                $lists->segments->each(function (Segment $segment) {
                    $this->query->union($this->segmentBuilder($segment));
                });
                $this->query->union($this->listImportedSubscriber($lists));
            }
        });
        return $this->query;
    }


    public function listImportedSubscriber(Lists $lists)
    {
        return $lists->subscribers()
            ->select($this->columns)
            ->withCount($this->withCount)
            ->with($this->with)
            ->when(count($this->statuses), function (EBuilder $builder) {
                $builder->whereIn('status_id', $this->statuses);
            })->orderBy('id', $this->orderBy)
            ->when($this->filters, function (EBuilder $builder) {
                $builder->filters($this->filters);
            });
    }

}
