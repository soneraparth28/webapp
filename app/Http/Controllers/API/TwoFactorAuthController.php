<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\Functions;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\TwoFactorCodes;
use Illuminate\Support\Facades\Validator;

class TwoFactorAuthController extends Controller
{
    use Functions;
}
