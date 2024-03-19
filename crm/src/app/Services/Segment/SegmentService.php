<?php


namespace App\Services\Segment;


use App\Models\Segment\Segment;
use App\Services\AppService;

class SegmentService extends AppService
{
    public function __construct(Segment $segment)
    {
        $this->model = $segment;
    }

}
