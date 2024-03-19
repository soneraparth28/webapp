<?php


namespace App\Repositories\Campaign;


use App\Helpers\Brand\DateRangeHelper;
use App\Models\Campaign\Campaign;
use App\Models\Email\EmailLog;
use App\Models\Lists\Lists;
use App\Models\Subscriber\Subscriber;
use App\Repositories\App\AppRepository;
use App\Repositories\App\StatusRepository;
use Illuminate\Database\Eloquent\Builder;

class CampaignRepository extends AppRepository
{
    use DateRangeHelper;
    public $status;

    public function __construct(Campaign $campaign, StatusRepository $status)
    {
        $this->model = $campaign;
        $this->builder = $campaign::query();
        $this->status = $status;
    }

    public function getEmailStatuses()
    {
        return $this->status
            ->statuses('email')
            ->pluck('id', 'name');
    }

    public function listSubscribers()
    {
        return $this->model->lists()->map(function (Lists $list) {
            if ($list->type == 'imported')
                return $list->subscribers;
            return $list->allSubscribers();
        })->flatten();
    }

    public function subscribers()
    {
        return Subscriber::query()
            ->where('brand_id', brand()->id)
            ->findMany(
                $this->model->subscriberAudiences()
            );
    }

    public function audiences()
    {
        $subscribers = $this->subscribers();

        return $this->listSubscribers()
            ->whereNotIn('id',  $subscribers->pluck('id'))
            ->merge($subscribers);
    }

    public function brandFilteredEmailLogs()
    {
        return EmailLog::query()
            ->when(request('brand_id'), function (Builder $builder) {
                $builder->whereHas('campaign', function (Builder $builder) {
                    $builder->where('brand_id', request('brand_id'));
                });
            });
    }
}
