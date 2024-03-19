<?php


namespace App\Repositories\Subscriber;


use App\Helpers\Brand\DateRangeHelper;
use App\Helpers\Core\Traits\Helpers;
use App\Managers\BulkImport\Subscriber\SubscriberImport;
use App\Models\Core\Builder\Form\CustomField;
use App\Models\Subscriber\Subscriber;
use App\Repositories\App\AppRepository;
use App\Repositories\App\StatusRepository;
use App\Repositories\Core\Status\StatusRepository as BaseStatus;
use App\Services\Core\Builder\Form\CustomFieldService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class SubscriberRepository extends AppRepository
{
    use Helpers, DateRangeHelper;
    public $status;
    public $importManager;

    public function __construct(Subscriber $subscriber, StatusRepository $status)
    {
        $this->model = $subscriber;
        $this->status = $status;
        $this->importManager = resolve(SubscriberImport::class);
        $this->builder = $subscriber::query();
    }


    /**
     * @param $subscribers
     * @param $isWithCF
     * @param \Closure $callback
     * @return int
     */
    public function chunkImported($subscribers, $isWithCF, \Closure $callback)
    {
        $count = 0;
        $meta = [
            'brand_id' => request('brand_id'),
            'status_id' => resolve(StatusRepository::class)->subscriberSubscribed(),
            'created_by' => auth()->id(),
        ];

        if (is_array($subscribers)) {
            $subscribers = $this->importManager->getSanitized($subscribers);
        }

        $chunks = $isWithCF ? $subscribers : $subscribers->chunk(600)->toArray();

        foreach ($chunks as $chunk) {
            if ($isWithCF) {
                $callback(array_merge($chunk, $meta));
                $count++;
            }
            else {
                $callback($this->fillChunks($chunk, $meta));
                $count += count($chunk);
            }
        }
        return $count;
    }

    public function fillChunks($chunks, $fillableMeta)
    {
        $fillableMeta['created_at'] = now();
        $fillableMeta['updated_at'] = now();
        return array_map(function ($chunk) use ($fillableMeta) {
            unset($chunk['custom_fields']);
            return array_merge($chunk, $fillableMeta);
        }, $chunks);
    }


    public function previewImported(?UploadedFile $file)
    {

        $validFields = $this->getValidFields(request('type'));

        $schema = $this->importManager->setFile($file);

        $columns = $schema->getColumns();

        if (count($columns) > count($validFields)) {
            $invalidColumns = array_diff($columns, $validFields);
        } else {
            $invalidColumns = array_diff($validFields, $columns);
        }

        if ($invalidColumns) {
            return \response([ 'valid' => $validFields ], 422);
        }

        return $this->importManager
            ->setFile($file)
            ->preview();
    }

    protected function getValidFields($type)
    {
        $customFields = resolve(CustomFieldService::class)->subscriberFields();
        $basicFields = ['first_name', 'last_name', 'email'];

        return !$type
            ? $basicFields
            : [...$basicFields, ...$customFields];
    }

    public function filterByStatus($names = [])
    {
        if (array_key_exists(0, $names) && is_string($names[0])) {
            $this->builder->whereHas('status', function (Builder $builder) use ($names){
                $builder->whereIn('name', $names)
                    ->where('type', 'subscriber');
            });
        }
        $this->builder->whereIn('status_id', $names);
        return $this;
    }

    public function rangedSubscribers($range = [])
    {
        $this->builder->when($range, function (Builder $builder) use ($range) {
            $builder->whereBetween('updated_at', $range);
        });
        return $this;
    }

    public function subscribedUnsubscribedStatuses()
    {
        $names = ['status_subscribed', 'status_unsubscribed'];
        return array_flip(
            resolve(StatusRepository::class)->subscriber(...$names)
        );
    }

    public function getCustomFields($brand_id = null)
    {
        return CustomField::with('customFieldType:id,name')
            ->where(function (Builder $builder) use ($brand_id) {
                $builder->where('brand_id', $brand_id ?? brand()->id)
                ->orWhereNull('brand_id');
            })
            ->where('context', 'subscriber')
            ->get();
    }


    public function getBulkIds($statuses = null): Collection
    {
        $statuses = is_array($statuses) ? $statuses : func_get_args();

        if (!$statuses) {
            $statuses = ['subscribed', 'unsubscribed'];
        }

        $statuses = Str::camel( "subscriber_" . join('_', $statuses));

        $statuses = resolve(BaseStatus::class)
            ->$statuses();


        return Subscriber::query()
            ->whereIn(
                'status_id',
                is_array($statuses) ? $statuses : [ $statuses ]
            )
            ->pluck('id');
    }
}
