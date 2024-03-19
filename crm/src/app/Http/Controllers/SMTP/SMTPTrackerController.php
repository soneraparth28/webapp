<?php


namespace App\Http\Controllers\SMTP;


use App\Http\Controllers\Controller;
use App\Webhook\SMTP\SMTPWebhookInitializer;

class SMTPTrackerController extends Controller
{
    private SMTPWebhookInitializer $hook;

    public function __construct(SMTPWebhookInitializer $hook)
    {
        $this->hook = $hook;
    }

    public function update($event, $tracker_id)
    {
        return $this->hook
            ->setEvent($event)
            ->init($tracker_id, request()->get('to'))
            ->handle();
    }

}