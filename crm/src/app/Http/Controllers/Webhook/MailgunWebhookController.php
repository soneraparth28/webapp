<?php

namespace App\Http\Controllers\Webhook;

use App\Http\Controllers\Controller;
use App\Webhook\Mailgun\MailgunWebhookInitializer;
use Illuminate\Http\Request;

class MailgunWebhookController extends Controller
{
    protected $hook;

    public function __construct(MailgunWebhookInitializer $initializer)
    {
        $this->hook = $initializer;
    }

    public function update(Request $request)
    {
        return $this->hook
            ->initialize($request)
            ->save();
    }
}
