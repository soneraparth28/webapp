<?php

namespace App\Http\Controllers\Subscriber;

use App\Exceptions\GeneralException;
use App\Filters\Subscriber\SubscriberFilter;
use App\Http\Controllers\Controller;
use App\Http\Requests\Subscriber\BulkDestroyRequest;
use App\Http\Requests\Subscriber\SubscriberStatusRequest;
use App\Repositories\Subscriber\SubscriberRepository;
use App\Services\Settings\APIKeyService;
use App\Services\Settings\BrandCustomFieldService;
use App\Models\{Core\App\Brand\Brand,
    Core\Auth\User,
    Form\CustomField,
    Subscriber\Traits\SubscriberRules,
    Subscriber\API};
use App\Models\Core\Builder\Form\CustomFieldValue;
use App\Models\Subscriber\Subscriber;
use App\Repositories\App\StatusRepository;
use App\Services\Subscriber\SubscriberService;
use Illuminate\{Database\Eloquent\Builder, Http\Request, Support\Facades\DB, Validation\ValidationException};

class SubscriberAPIController extends Controller
{
    use SubscriberRules;

    public function __construct(SubscriberService $subscriber, SubscriberFilter $filter)
    {
        $this->service = $subscriber;
        $this->filter = $filter;
    }

    public function view()
    {
        $statuses = resolve(StatusRepository::class)->subscriber('status_subscribed');

        return $this->service
            ->filters($this->filter)
            ->select(['id', 'email', 'first_name', 'last_name'])
            ->latest('id')
            ->whereIn('status_id', array_keys($statuses))
            ->paginate(50);
    }

    public function updateStatus(SubscriberStatusRequest $request)
    {
        $this->service->prepareSubscribersAttrs(
            $request->only('isBulkAction', 'subscribers', 'pickOnly')
        )->changeStatus(
            $request->get('status')
        );

        return updated_responses('status');
    }

    public function bulkDestroy(BulkDestroyRequest $request)
    {
        return DB::transaction(function () use ($request) {
            $ids = $this->service->prepareSubscribersAttrs(
                $request->only('isBulkAction', 'subscribers', 'pickOnly')
            )->getAttr('subscribers');

            $deletable = $this->service->whereDoesntHave('emailLogs')
                ->findMany($ids)
                ->pluck('id');

            CustomFieldValue::query()->whereIn('contextable_id', $deletable)
                ->where('contextable_type', Subscriber::class)
                ->delete();

            $count = $this->service->whereIn('id', $deletable)->delete();

            $dependents = $this->service->whereHas('emailLogs')
                ->findMany($ids)
                ->pluck('full_name')
                ->take(10)
                ->join(', ');

            return response([
                'status' => $count != 0,
                'message' => !$count ? '' : "$count " . trans('default.deleted_response', ['name' => trans('default.subscribers')]),
                'error_message' => !$dependents ? '' : trans('default.resource_can_not_be_deleted', [
                    'resource' => trans('default.subscribers'),
                ])
            ], !$count ? 424 : 200);
        });
    }

    public function getApiUrl($regenerate = false)
    {
        $api = API::query()
            ->where('brand_id', brand()->id)
            ->firstOrNew();

        if (!$api->exists() || $regenerate) {
            $key = hash_hmac('sha256', rand(0, 1000), config('api.key'));

            $user = User::whereHas('roles', function (Builder $builder) {
                $builder->where('is_admin', 1)
                    ->where('is_default', 1)
                    ->where('brand_id', brand()->id);
            })->first();

            $api->fill([
                'brand_id' => brand()->id,
                'user_id' => optional($user)->id ?? auth()->id(),
                'updated_by' => auth()->id(),
                'key' => $key
            ]);

            $api->save();
        }

        return [
            'api_key' => $api->key,
            'url' => [
                'store' => route('subscriber-external-api', [
                    'brand' => brand()->short_name
                ]),
                'update' => route('subscriber-update-api', [
                    'brand' => brand()->short_name,
                ])
            ],
            'custom_fields' => CustomField::query()->get(['meta', 'name'])
        ];
    }

    public function store($brand)
    {
        $brand = Brand::finByShortNameOrIdCached($brand);

        $attributes = request()->merge(['brand_id' => $brand->id])
            ->validate($this->APICreatedRules());

        $api = resolve(APIKeyService::class)
            ->checkAPIKey($brand->id, \request('api_key'));

        $attributes['created_by'] = $api->user_id;
        $attributes['status_id'] = resolve(StatusRepository::class)
            ->subscriberSubscribed();

        $subscriber = $this->service
            ->save($attributes);

        if (request()->has('custom_fields')) {
            resolve(BrandCustomFieldService::class)
                ->setSubscriber($subscriber)
                ->updateCustomFields(request('custom_fields'));
        }

        return response()->json([
            'status' => true,
            'message' => trans('default.thanks_for_subscribing_to_our_newsletter')
        ]);
    }

    public function update($brand, Request $request)
    {
        $brand = Brand::finByShortNameOrIdCached($brand);

        $attributes = $request->validate(['email' => 'required|email']);
        $subscriber = Subscriber::whereEmail($attributes['email'])
            ->whereBrandId($brand->id)
            ->firstOrFail();

        $attributes = $request->merge(['brand_id' => $brand->id])
            ->validate($this->APIUpdatedRules());

        $api = resolve(APIKeyService::class)
            ->checkAPIKey($brand->id, \request('api_key'));

        $attributes['created_by'] = $api->user_id;
        $attributes['status_id'] = resolve(StatusRepository::class)
            ->subscriberSubscribed();

        $subscriber
            ->fill($attributes)
            ->save();

        if ($request->has('custom_fields')) {
            resolve(BrandCustomFieldService::class)
                ->setAttributes(['brand_id' => $brand->id])
                ->updateCustomFields(
                    $request->get('custom_fields'),
                    fn($id, $value) => $subscriber->customFields()
                        ->updateOrCreate(
                            ['custom_field_id' => $id],
                            ['value' => $value]
                        ),
                    true
                );
        }

        return updated_responses('subscriber');

    }
}
