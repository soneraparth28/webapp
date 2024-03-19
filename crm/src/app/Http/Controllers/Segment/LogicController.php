<?php

namespace App\Http\Controllers\Segment;

use App\Http\Controllers\Controller;
use App\Models\Segment\LogicName;

class LogicController extends Controller
{
    public function index()
    {
        return LogicName::with('operator:id,name,type')
            ->get(['id', 'name', 'type']);
    }
}
