<?php

namespace App\Http\Controllers\Lists;

use App\Filters\Lists\ListFilter;
use App\Http\Controllers\Controller;
use App\Http\Requests\Lists\ListsRequest as Request;
use App\Models\Lists\Lists;
use App\Notifications\CampaignNotification;
use App\Notifications\Lists\ListNotification;
use App\Repositories\App\StatusRepository;
use App\Services\Lists\ListService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class ListController extends Controller
{
    public function __construct(ListService $service, ListFilter $filter)
    {
        $this->service = $service;
        $this->filter = $filter;
    }

    public function index()
    {
        return $this->service
            ->withCount([
                'subscribers' => function(Builder $builder) {
                    $statuses = resolve(StatusRepository::class)
                        ->cachedSubscriberStatus();

                    $builder->whereIn('status_id', $statuses);
                },
                'segments'
            ])
            ->latest('id')
            ->filters($this->filter)
            ->paginate(
                request('per_page', 10)
            );
    }

    public function create()
    {
        return view('brands.list.create');
    }
    public function store(Request $request)
    {
        $list = DB::transaction(function () {
            return  $this->service->save();
        });

        notify()
            ->on('list_created')
            ->with($list)
            ->send(ListNotification::class);

        return created_responses('list', [
            'list' => $list
        ]);
    }
    public function edit($id)
    {
        return view('brands.list.create', compact('id'));
    }

    public function show(Lists $list)
    {
        $statuses = resolve(StatusRepository::class)
            ->cachedSubscriberStatus();

        $unSubscriberStatuses = resolve(StatusRepository::class)
            ->subscriberUnsubscribed();

        if ($list->type == 'imported'){
            $list->loadCount('subscribed', 'unsubscribed', 'segments');
            $list->segment_subscriber_count = $list->subscriberBuilder($statuses)
                ->count();
            unset($list->segments);
            unset($list->subscribers);
            return $list;
        }

        $list->loadCount('segments');
        $list->subscribed_count = $list->dynamicSubscribers($statuses)->count();
        $list->unsubscribed_count = $list->dynamicSubscribers($unSubscriberStatuses)->count();
        $list->segment_subscriber_count = $list->subscriberBuilder($statuses)->count();
        unset($list->segments);
        unset($list->subscribers);
        return $list;
    }

    public function update(Request $request, Lists $list)
    {
        $list = DB::transaction(function () use ($list) {

            $this->service->setModel($list);
            return $this->service->save();
        });

        return updated_responses('list', [
            'list' => $list
        ]);
    }

    public function destroy(Lists $list)
    {
        if ($list->delete()) {
            notify()
                ->on('list_deleted')
                ->with((object)$list->toArray())
                ->send(ListNotification::class);
            return deleted_responses('list');
        }

        return failed_responses();
    }
}
