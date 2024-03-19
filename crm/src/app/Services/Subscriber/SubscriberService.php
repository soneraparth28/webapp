<?php


namespace App\Services\Subscriber;


use App\Filters\Traits\CollectionHelper;
use App\Helpers\Brand\DateRangeHelper;
use App\Helpers\Core\Traits\HasWhen;
use App\Helpers\Core\Traits\Helpers;
use App\Models\Subscriber\Subscriber;
use App\Repositories\Subscriber\SubscriberRepository;
use App\Services\AppService;
use App\Services\Settings\BrandCustomFieldService;
use App\Services\Subscriber\Helpers\BulkActionTrait;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;

class SubscriberService extends AppService
{
    public SubscriberRepository $repository;
    private Collection $customFields;
    private Collection $dateTypeCustomFieldIds;
    private Collection $customFieldMetas;
    /**
     * @var BrandCustomFieldService
     */
    private BrandCustomFieldService $cfService;
    use Helpers, CollectionHelper, DateRangeHelper, HasWhen, BulkActionTrait;

    public function __construct(
        Subscriber $subscriber,
        SubscriberRepository $repository,
        BrandCustomFieldService $cfService
    )
    {
        $this->model = $subscriber;
        $this->repository = $repository;
        $this->cfService = $cfService;
    }

    public function subscribers()
    {
        $blackListed = $this->repository->status->subscriberBlacklisted();
        return $this->model::query()
            ->when(request('black_listed'), function (Builder $builder) use ($blackListed) {
                $builder->with('status:id,name,class', 'lists:name,id', 'lastActivity:updated_at')
                    ->withCount('sent', 'delivered')
                    ->where('status_id', $blackListed);
            }, function (Builder $builder) use ($blackListed) {
                $builder->with(
                            'customFields:value,contextable_type,contextable_id,custom_field_id',
                    'status:id,name,class',
                    'lists:name,id',
                    'lastActivity:updated_at',
                    'emailLogs:id,subscriber_id,campaign_id,email_date,open_count,click_count,delivery_server,status_id'
                )->withCount('sent', 'delivered')
                    ->where('status_id', '<>', $blackListed);
            });
    }

    public function store()
    {
        $subscriber = parent::save(
            array_merge(request()->all(), [
                'status_id' => $this->repository->status->subscriberSubscribed()
            ])
        );
        if (request()->has('list'))
            $this->model->lists()->attach(
                request('list')
            );

        if (request()->has('custom_fields')) {
            $this->cfService
                ->setAttr('brand_id', brand()->id)
                ->setSubscriber($subscriber)
                ->updateCustomFields(
                    request('custom_fields')
                );
        }

        return $this->model;
    }

    public function changeStatus($status, $subscribers = [])
    {
        $subscribers = count($subscribers)
            ? $subscribers
            :  $this->getAttr('subscribers');

        $subscribers = $this->checkMakeArray($subscribers);

        if (is_string($status)) {
            $name = 'subscriber' . ucfirst($status);
            $status = $this->repository->status->{$name}();
        }

        $this->model::query()
            ->whereIn('id', $subscribers)
            ->update([ 'status_id' =>  $status ]);
    }

    public function quickImport(?UploadedFile $subscribers, $type)
    {
        return $this->saveImported(
            $subscribers,
            $type
        );
    }

    public function saveImported($subscribers, $isWithCF): int
    {
        if ($isWithCF) {
            $this->cfService->setCurrentCustomFields(brand()->id);
        }

        return $this->repository->chunkImported($subscribers, $isWithCF, function ($chunk) use($isWithCF) {
            if ($isWithCF) {
                $subscriber = $this->model::create($chunk);
                $this->when($chunk['custom_fields'], fn (SubscriberService $service, $fields) => [
                    $service->cfService->prepareCustomFields(
                        $fields,
                        $this->cfService->storeCustomFields($subscriber),
                        true
                    )
                ]);
            }
            else {
                $this->model::insert($chunk);
            }
        });

    }


    public function subscribersCount($range_type)
    {
        $dateRange = $this->dateRange($range_type);
        $statuses =  $this->repository->subscribedUnsubscribedStatuses();

        $grossSubscribers = $this->grossSubscribers($statuses);

        $countGroups = $grossSubscribers->filter(function ($subscriber) use ($dateRange) {
            if (!$dateRange->range) return true;
            return $this->inDateBetween(
                $subscriber->updated_at, $dateRange->range
            );
        })->groupBy(function ($row) use ($dateRange) {
                return Carbon::parse($row->updated_at)->format($dateRange->context);
            })->map(function ($rows) use ($statuses) {
                return $this->statusCounter($rows, $statuses);
            });

        return $dateRange->range
            ? $this->fillPeriod($countGroups, $dateRange)
            : $countGroups;
    }

    private function grossSubscribers($statuses)
    {
        return \DB::table('subscribers')
            ->when(request('brand_id'), function (\Illuminate\Database\Query\Builder $builder) {
                $builder->where('brand_id', request('brand_id'));
            })
            ->whereIn('status_id', $statuses)
            ->select('status_id', 'updated_at')
            ->get();
    }

    public function grossCount()
    {
        $statuses = $this->repository->subscribedUnsubscribedStatuses();
        $subscribers = $this->grossSubscribers(array_values($statuses));
        return $this->statusCounter($subscribers, $statuses);
    }

    private function statusCounter($rows, array $statuses)
    {
        return [
            'subscribed' => $rows->whereIn('status_id', [ $statuses['status_subscribed'] ])->count(),
            'unsubscribed' => $rows->whereIn('status_id', [ $statuses['status_unsubscribed'] ])->count()
        ];
    }

    public function fillPeriod($countGroups, $dateRange)
    {
        return $this->repository->fillCounts($countGroups, $dateRange, function ($dateInfo) use ($countGroups) {
            if (!$countGroups->keys()->contains($dateInfo['context'])) {
                return $this->fillAssoc(['subscribed', 'unsubscribed'], 0, $dateInfo);
            }
            return array_merge($countGroups[$dateInfo['context']], $dateInfo);
        });
    }


}
