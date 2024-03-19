<?php

namespace App\Http\Controllers\Segment;

use App\Filters\Segment\SegmentFilter;
use App\Http\Controllers\Controller;
use App\Services\Segment\SegmentService;

class SegmentCountController extends Controller
{
    public function __construct(SegmentService $service, SegmentFilter $filter)
    {
        $this->service = $service;
        $this->filter = $filter;
    }
    public function index()
    {
        return $this->service
            ->filters($this->filter)
            ->count();
    }
}
