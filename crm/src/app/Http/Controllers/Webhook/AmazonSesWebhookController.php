<?php

namespace App\Http\Controllers\Webhook;

use App\Http\Controllers\Controller;
use App\Webhook\SES\SESWebhookInitializer;
use App\Webhook\SES\Traits\SESSubscriptionConfirm;
use Illuminate\Http\Request;

class AmazonSesWebhookController extends Controller
{
    use SESSubscriptionConfirm;

    protected $hook;

    public function __construct(SESWebhookInitializer $initializer)
    {
        $this->hook = $initializer;
    }

    public function update(Request $request)
    {
        if ($request->header('X-Amz-Sns-Message-Type') == 'SubscriptionConfirmation') {
            return $this->whenSubscriptionConfirmation($request);
        }

        return $this->hook
            ->initialize($request)
            ->save();

    }
}
