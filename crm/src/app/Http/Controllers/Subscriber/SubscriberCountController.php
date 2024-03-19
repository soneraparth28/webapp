<?php

namespace App\Http\Controllers\Subscriber;

use App\Filters\Subscriber\SubscriberFilter;
use App\Helpers\Brand\DateRangeHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\App\DateRangeRequest as Request;
use App\Services\Subscriber\SubscriberService;

class SubscriberCountController extends Controller
{
    use DateRangeHelper;
    public function __construct(SubscriberService $subscriber, SubscriberFilter $filter)
    {
        $this->service = $subscriber;
        $this->filter = $filter;
    }

    public function index(Request $request, $range_type = 0)
    {
        if ($range_type === 'gross') {
            return $this->service->grossCount();
        }
        return $this->service->subscribersCount($range_type);

    }
}
