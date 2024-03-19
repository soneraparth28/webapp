<?php

namespace App\Http\Controllers\CustomField;

use App\Http\Controllers\Controller;
use App\Repositories\CustomField\CustomFieldRepository;

class CustomFieldController extends Controller
{
    private $repository;

    public function __construct(CustomFieldRepository $repository)
    {
        $this->repository = $repository;
    }

    public function index($context = 'subscriber')
    {
        return $this->repository
            ->fields($context);
    }
}
