<?php

namespace App\Http\Controllers\Segment;

use App\Http\Controllers\Controller;
use App\Models\Segment\LogicOperator;

class OperatorController extends Controller
{
    public function index()
    {
        return LogicOperator::all('id', 'name', 'type');
    }
}
