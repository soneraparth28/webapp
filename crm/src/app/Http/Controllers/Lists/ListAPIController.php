<?php

namespace App\Http\Controllers\Lists;

use App\Helpers\Traits\NumberHelper;
use App\Http\Controllers\Controller;
use App\Models\Lists\Lists;
use App\Repositories\App\StatusRepository;
use App\Services\Lists\ListService;

class ListAPIController extends Controller
{
    use NumberHelper;
    public function __construct(ListService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        return $this->service
            ->latest('id')
            ->get(['id', 'name']);
    }

    public function copy($id)
    {
        return view('brands.list.create', ['id' => $id, 'action' => 'copy']);
    }

    public function view(Lists $lists)
    {
//        return $lists->load('subscribers:id', 'segments:id');
        return $lists->load('segments:id')->loadCount('subscribers');
    }

    public function count(Lists $lists)
    {
        $statuses = resolve(StatusRepository::class)
            ->cachedSubscriberStatus();

        $count = $lists->subscriberBuilder($statuses)
            ->union($lists->subscribers()->select('subscribers.*')->whereIn('status_id', $statuses))
            ->count();

        return $this->numberFormatter($count) ?: 'N/A';
    }
}
