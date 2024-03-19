<?php

namespace App\Http\Controllers\Segment;

use App\Http\Controllers\Controller;
use App\Services\Segment\SegmentService;

class SegmentLogicCountController extends Controller
{
    public function __construct(SegmentService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        return $this->service
            ->selectRaw('MAX(JSON_LENGTH(`segment_logic`)) as max_length, MIN(JSON_LENGTH(`segment_logic`)) as min_length')
            ->first();
    }
}
