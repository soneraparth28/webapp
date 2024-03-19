<?php


namespace App\Services\Campaign;


use App\Helpers\Traits\HasMemoization;
use App\Models\Campaign\Campaign;
use App\Repositories\App\StatusRepository;
use App\Services\AppService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class CampaignSubscribersService extends AppService
{
    use HasMemoization;

    /**
     * @var StatusRepository
     */
    private $status;

    public function __construct(StatusRepository $status)
    {
        $this->status = $status;
    }
    public function subscribers(Campaign $campaign)
    {
        $statuses = resolve(StatusRepository::class)
            ->cachedSubscriberStatus();

        return $campaign->subscriberBuilder($statuses);
    }

    /**
     * @param Campaign $campaign
     * @return Collection
     */
    public function listSubscriber(Campaign $campaign)
    {
        return $this->memoize('list-subscriber-'.$campaign->id, function () use ($campaign) {
            $audience = $campaign->audiences->firstWhere('audience_type', '=', 'list');
            $statuses = resolve(StatusRepository::class)
                ->cachedSubscriberStatus();
            return optional($audience)->listAudiences($statuses) ?? collect([]);
        });
    }

    public function listSubscriberCount(Campaign $campaign)
    {
        $statuses = resolve(StatusRepository::class)
            ->cachedSubscriberStatus();

        return $campaign->listSubscriberBuilder($statuses)->count();
    }

    public function counts(Campaign $campaign)
    {
        $statuses = resolve(StatusRepository::class)
            ->cachedSubscriberStatus();
        $subscribers = $campaign->subscriberBuilder([], ['id', 'status_id'])->get();

        if (!request()->has('load_audiences')){
            $campaign->loadCount(['emailLogs as sent_count' => function(Builder $builder) {
                $builder->where('status_id', $this->status->emailSent());
            }]);
        }

        return [
            'subscribers' => $subscribers->whereIn('status_id', $statuses)->count(),
            'unsubscribed' => $subscribers->where('status_id', $this->status->subscriberUnsubscribed())
                ->count()
        ];
    }
}
