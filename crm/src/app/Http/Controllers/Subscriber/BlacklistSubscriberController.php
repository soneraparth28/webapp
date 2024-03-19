<?php

namespace App\Http\Controllers\Subscriber;

use App\Filters\Core\BaseFilter;
use App\Helpers\Core\Traits\Helpers;
use App\Http\Controllers\Controller;
use App\Http\Requests\Subscriber\BlackListSubscriberRequest as Request;
use App\Notifications\Subscriber\SubscriberBlackListNotification;
use App\Services\Subscriber\Helpers\BulkActionTrait;
use App\Services\Subscriber\SubscriberService;

class BlacklistSubscriberController extends Controller
{
    use Helpers, BulkActionTrait;

    public function __construct(SubscriberService $service, BaseFilter $filter)
    {
        $this->service = $service;
        $this->filter = $filter;
    }

    public function index()
    {
        return view('brands.subscribers.blacklisted');
    }

    public function store(Request $request)
    {
        $this->service
            ->prepareSubscribersAttrs([
                'isBulkAction' => $request->get('isBulkAction', false),
                'subscribers' => $request->get('subscribers')
            ])
            ->changeStatus('blacklisted');

//        if (count($this->checkMakeArray($request->get('subscribers', [])))) {
//            notify()
//                ->on('subscribers_blacklisted')
//                ->with(brand())
//                ->send(SubscriberBlackListNotification::class);
//        }

        return status_response('subscribers', 'blacklisted');
    }

    public function update(Request $request)
    {
        $this->service
            ->setAttrs($request->only('subscribers'))
            ->changeStatus('subscribed');

        return status_response('subscribers', 'subscribed');
    }
}
