<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\App\DateRangeRequest as Request;
use App\Services\Email\EmailLogService;

class EmailStatisticsController extends Controller
{
    public function __construct(EmailLogService $service)
    {
        $this->service = $service;
    }

    public function show(Request $request, $range_type = 0)
    {
        if ($range_type === 'gross') {
            return $this->service->grossStats();
        }
        return $this->service->stats( $range_type );
    }
}
