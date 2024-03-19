<?php

namespace App\Http\Controllers\Segment;

use App\Filters\Segment\SegmentFilter;
use App\Helpers\Traits\NumberHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Segment\SegmentRequest as Request;
use App\Models\Segment\Segment;
use App\Notifications\Segment\SegmentNotification;
use App\Services\Segment\SegmentService;

class SegmentController extends Controller
{
    use NumberHelper;
    public function __construct(SegmentService $service, SegmentFilter $filter)
    {
        $this->service = $service;
        $this->filter = $filter;
    }

    public function index()
    {
         $paginated = $this->service
             ->filters($this->filter)
             ->latest('id')
             ->paginate(request('per_page', 10));

         $segments = $paginated->each(function (Segment $segment) {
             $segment->segment_logic_count = count(array_merge(...$segment->segment_logic));
             unset($segment->segment_logic);
         });

         $response = $paginated->toArray();

         $response['data'] = $segments;

         return $response;

    }

    public function view()
    {
        return $this->service
            ->latest('id')
            ->get(['id', 'name']);
    }

    public function create()
    {
        return view('brands.segments.segment');
    }

    public function store(Request $request)
    {
        $segment = $this->service
            ->save();

        notify()
            ->on('segment_created')
            ->with($segment)
            ->send(SegmentNotification::class);

        return created_responses('segment', [
            'segment' => $segment
        ]);
    }

    public function show(Segment $segment)
    {
        return $segment;
    }

    public function edit($id)
    {
        return view('brands.segments.segment', ['id' => $id]);
    }


    public function update(Segment $segment, Request $request)
    {
        $segment->fill($request->all());

        if ($segment->isDirty()){
            notify()
                ->on('segment_updated')
                ->with($segment)
                ->send(SegmentNotification::class);
        }
        $segment->save();

        return updated_responses('segment', [
            'segment' => $segment
        ]);
    }


    public function destroy(Segment $segment)
    {
        $segment->delete();

        notify()
            ->on('segment_deleted')
            ->with((object)$segment->toArray())
            ->send(SegmentNotification::class);

        return  deleted_responses('segment');
    }

}
