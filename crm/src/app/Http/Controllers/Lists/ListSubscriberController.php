<?php

namespace App\Http\Controllers\Lists;

use App\Builder\Lists\SubscriberBuilder;
use App\Filters\Subscriber\SubscriberFilter;
use App\Http\Controllers\Controller;
use App\Http\Requests\Subscriber\ListSubscriberRequest as Request;
use App\Models\Lists\Lists;
use App\Repositories\App\StatusRepository;
use App\Services\Lists\ListService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class ListSubscriberController extends Controller
{
    public function __construct(ListService $service, SubscriberFilter $filter)
    {
        $this->filter = $filter;
        $this->service = $service;
    }

    public function store(Request $request)
    {
        DB::transaction(function () use($request) {

            if ($request->has('name')) {
                $list = $this->service->fill(
                    $request->only('name', 'brand_id')
                );
                $list->save();
            }

            $this->service->prepareSubscribersAttrs(
                $request->only('isBulkAction', 'subscribers')
            )->when(
                isset($list),
                fn($service) => $service->setAttr('lists', [ $list->id ]),
                fn($service) => $service->mergeAttrs($request->only('lists'))
            )
            ->addBulkListSubscribers();

            return true;
        });

        return attached_response('list');
    }


    public function show(Lists $list)
    {
        $statuses = resolve(StatusRepository::class)
            ->subscriber('status_subscribed', 'status_unsubscribed', 'status_blacklisted');

        $fields = ['id', 'first_name', 'last_name', 'email', 'status_id'];

        if($list->type == 'imported') {
             return $list->subscribers()
                 ->select($fields)
                 ->with('status:name,id,class')
                 ->withCount('sent', 'delivered')
                 ->whereIn('status_id', array_keys($statuses))
                 ->filters($this->filter)
                 ->latest()
                 ->paginate(request()->get('per_page', 15));
        }

        return (new SubscriberBuilder($list->segments))
            ->select($fields)
            ->with( 'status:name,id,class', 'sent:id,subscriber_id', 'delivered:id,subscriber_id')
            ->filters($this->filter)
            ->latest()
            ->whereStatus(array_keys($statuses))
            ->build()
            ->union($list->subscribers()
                ->with( 'status:name,id,class', 'sent:id,subscriber_id', 'delivered:id,subscriber_id')
                ->select(count($fields) ? $fields : 'subscribers.*')
                ->when(count($statuses), function (Builder $builder) use($statuses) {
                    $builder->whereIn('status_id', array_keys($statuses));
                }))
            ->paginate();
    }

    public function destroy(Request $request)
    {
        $this->service->prepareSubscribersAttrs(
            $request->only('isBulkAction', 'subscribers')
        )
        ->removeBulkListSubscribers(
            $request->get('lists')
        );

        return detached_response('list');
    }



}
