<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\Functions;
use Illuminate\Http\Request;
use App\Models\AdminSettings;
use App\Models\Notifications;
use App\Models\Plans;
use Illuminate\Http\Response;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Log;
use Laravel\Cashier\Cashier;
use Laravel\Cashier\Http\Controllers\WebhookController;
use Laravel\Cashier\Subscription;
use App\Models\PaymentGateways;
use App\Models\Transactions;
use App\Models\Deposits;
use Stripe\PaymentIntent as StripePaymentIntent;
use App\Models\User;

class StripeWebHookController extends Controller
{
    use Functions;
}
