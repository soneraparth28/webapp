<?php

namespace App\Http\Controllers\Status;

use App\Http\Controllers\Controller;
use App\Repositories\App\StatusRepository;

class StatusAPIController extends Controller
{
    public $repository;

    public function __construct(StatusRepository $repository)
    {
        $this->repository = $repository;
    }

    public function index($type)
    {
        return $this->repository
            ->statuses($type);
    }
}
