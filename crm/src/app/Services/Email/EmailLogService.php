<?php


namespace App\Services\Email;


use App\Helpers\Brand\DateRangeHelper;
use App\Helpers\Core\Traits\Helpers;
use App\Models\Campaign\Campaign;
use App\Models\Email\EmailLog;
use App\Models\Subscriber\Subscriber;
use App\Repositories\App\StatusRepository;
use App\Repositories\Email\EmailLogRepository;
use App\Services\AppService;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class EmailLogService extends AppService
{
    use DateRangeHelper, Helpers;

    private EmailLogRepository $repository;

    public function __construct(EmailLog $emailLog, EmailLogRepository $repository)
    {
        $this->model = $emailLog;
        $this->repository = $repository;
    }

    public function stats($range_type, $campaign_id = null): Collection
    {
        $dateRange = $this->dateRange($range_type);
        $logs = $this->repository->filteredEmailLogs($campaign_id, $dateRange->range);

        $countGroups = $logs->groupBy(function ($row) use ($dateRange) {
            return Carbon::parse($row->updated_at)->format($dateRange->context);
        })->map(function ($rows) {
            return $this->rateCountGenerate($rows);
        });

        return $dateRange->range
            ? $this->fillPeriods($countGroups, $dateRange)
            : $countGroups;
    }

    public function fillPeriods($countGroups, $dateRange): Collection
    {
        $fillableKeys = $this->repository->fillableStatuses();
        return $this->fillCounts($countGroups, $dateRange, function ($dateInfo) use ($fillableKeys, $countGroups) {

            $fillValues = ['count' => 0, 'rate' => 0];
            $context = $dateInfo['context'];

            if (!$countGroups->keys()->contains($context))
                return array_merge(
                    ['counts' => $this->fillAssoc($fillableKeys, $fillValues)], $dateInfo
                );

            $restFillAbles = $fillableKeys->diff($countGroups[$context]->keys())->values();
            $countGroup = $countGroups[$context]->toArray();
            if ($restFillAbles->isNotEmpty())
                return array_merge(
                    ['counts' => $this->fillAssoc($restFillAbles, $fillValues, $countGroup)],
                    $dateInfo
                );

            return array_merge(['counts' => $countGroup], $dateInfo);
        });
    }

    public function grossStats($campaign_id = null): Collection
    {
        $emails = $this->repository->filteredEmailLogs($campaign_id);
        return $this->rateCountGenerate($emails);
    }

    private function rateCountGenerate(Collection $rows): Collection
    {
        $groupsKeys = $rows->groupBy('status')
            ->keys()
            ->concat(['status_clicked', 'status_open']);

        $fillableKeys = $this->fillableKeysFactory($rows);

        return $fillableKeys->map(function ($values, $key) use ($groupsKeys) {
            if ($groupsKeys->contains($key) || $key === 'status_sent') {
                return array_merge([
                    'status' => $key
                ], $values);
            }
            return [ 'count' => 0, 'rate' => 0, 'status' => $key ];
        })->keyBy('status');
    }

    public function fillableKeysFactory($rows): Collection
    {
        $totalRows = $rows->count();
        $total_sent = $rows->where('status', '!=', 'status_bounced')->count();
        $total_open = $rows->where('open_count','<>', 0)->count();
        $total_delivered = $rows->where('status', 'status_delivered')->count();
        $total_bounced = $rows->where('status', 'status_bounced')->count();
        $total_clicked = $rows->where('click_count','<>', 0)->count();
        return collect([
            'status_sent' => $this->generateRowStats($total_sent, $totalRows),
            'status_open' => $this->generateRowStats($total_open, $total_delivered),
            'status_bounced' =>  $this->generateRowStats($total_bounced, $totalRows),
            'status_delivered' => $this->generateRowStats($total_delivered, $total_sent),
            'status_clicked' => $this->generateRowStats($total_clicked, $total_delivered),
        ]);
    }

    public function generateRowStats($count, $total): array
    {
        return [
            'count' => $count,
            'rate' => $total
                ? round((($count / $total) * 100), 2)
                : 0,
        ];
    }

    public function store(Campaign $campaign, $subscriber, $message, $id)
    {
        $method = config('mail.driver') == 'smtp' ? 'emailDelivered' : 'emailSent';
        $status = resolve(StatusRepository::class)
            ->$method();

        $this->model::create([
            'subscriber_id' => $subscriber->id,
            'email_id' => $id,
            'campaign_id' => $campaign->id,
            'email_date' => Carbon::now(),
            'email_content' => $message,
            'open_count' => 0,
            'click_count' => 0,
            'delivery_server' => config('mail.driver'),
            'status_id' => $status,
            'tracker_id' => $this->getAttr('tracker_id')
        ]);
    }
}
