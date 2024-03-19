<?php

namespace App\Http\Controllers\Segment;

use App\Helpers\Traits\NumberHelper;
use App\Http\Controllers\Controller;
use App\Models\Segment\Segment;
use App\Repositories\App\StatusRepository;

class SegmentSubscriberController extends Controller
{
    use NumberHelper;

    public function show(Segment $segment)
    {
        $statuses = resolve(StatusRepository::class)
            ->cachedSubscriberStatus();

        $count = $segment->subscribersCount($statuses);

        return  $count > 0 ? $this->numberFormatter($count) : 'N/A';
    }
}
