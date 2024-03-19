<?php

namespace App\Http\Controllers\Subscriber;

use App\Filters\Subscriber\SubscriberFilter;
use App\Http\Controllers\Controller;
use App\Http\Requests\Subscriber\SubscriberRequest as Request;
use App\Models\Subscriber\Subscriber;
use App\Notifications\Subscriber\SubscriberNotification;
use App\Services\Settings\BrandCustomFieldService;
use App\Services\Subscriber\SubscriberService;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;

class SubscriberController extends Controller
{
    public function __construct(SubscriberService $subscriber, SubscriberFilter $filter)
    {
        $this->service = $subscriber;
        $this->filter = $filter;
    }

    public function index()
    {
        return $this->service
            ->subscribers()
            ->orderBy('id', 'DESC')
            ->filters($this->filter)
            ->paginate(\request()->get('per_page', 10));
    }

    public function create()
    {
        return view('brands.subscribers.create');
    }

    public function store(Request $request)
    {
        $subscriber = $this->service->store();

        notify()
            ->on('subscriber_created')
            ->with($subscriber)
            ->send(SubscriberNotification::class);

        return created_responses('subscriber');
    }

    public function show(Subscriber $subscriber)
    {
        return $subscriber->load(
            'lists:id,name',
            'customFields:id,value,custom_field_id,contextable_type,contextable_id',
            'customFields.customField:id,name'
        );
    }

    public function edit($id)
    {
        return view('brands.subscribers.create', ['id' => $id]);
    }


    public function update(Subscriber $subscriber, Request $request)
    {
        DB::transaction(function () use($subscriber, $request) {
            $subscriber->fill($request->all());
            if ($subscriber->isDirty()){
                notify()
                    ->on('subscriber_updated')
                    ->with($subscriber)
                    ->send(SubscriberNotification::class);
            }
            $subscriber->save();

            if ($request->has('custom_fields')) {
                resolve(BrandCustomFieldService::class)
                    ->setAttr('brand_id', brand()->id)
                    ->setSubscriber($subscriber)
                    ->updateCustomFields(
                        $request->get('custom_fields'),
                        fn ($id, $value) => $subscriber->customFields()
                            ->updateOrCreate(
                                ['custom_field_id' => $id],
                                ['value' => $value]
                            )
                );
            }
            if ($request->has('list'))
                $subscriber->lists()->sync($request->list);

            return $subscriber;
        });

        return updated_responses('subscriber');
    }

    public function destroy(Subscriber $subscriber)
    {
        if ($subscriber->emailLogs->count()) {
            return response(['status' => false, 'message' => trans('default.resource_can_not_be_deleted',['resource'=>trans('default.subscriber')])], 424);
        }
        return DB::transaction(function () use($subscriber) {
            $subscriber->customFields()->delete();
            $subscriber->delete();
            notify()
                ->on('subscriber_deleted')
                ->with((object)$subscriber->toArray())
                ->send(SubscriberNotification::class);
            return deleted_responses('subscriber');
        });
    }
}
