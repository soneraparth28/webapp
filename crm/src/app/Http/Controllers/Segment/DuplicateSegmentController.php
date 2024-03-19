<?php

namespace App\Http\Controllers\Segment;

use App\Http\Controllers\Controller;
use App\Models\Segment\Segment;
use App\Services\Segment\SegmentService;

class DuplicateSegmentController extends Controller
{
    public function __construct(SegmentService $service)
    {
        $this->service = $service;
    }

    public function show($id)
    {
        return view('brands.segments.segment', ['id' => $id, 'action' => 'copy']);
    }

    public function store(Segment $segment)
    {
        $segment = $this->service->duplicate($segment);

        return duplicated_response('segment', [
            'segment' => $segment
        ]);
    }
}
