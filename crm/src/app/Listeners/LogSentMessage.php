<?php

namespace App\Listeners;

use App\Services\Email\EmailLogService;

class LogSentMessage
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle($event)
    {
        if (isset($event->data['last'])) {
            if ($event->data['last'] === 'disable-log')
                return;
            $subscriber = $event->data['subscriber'];
            $campaign = $event->data['campaign'];
            /**
             * @var \Illuminate\Mail\Message $message
             */
            $message = $event->data['message'];

            $message_id = $message->getId();

            if (config('mail.driver') == 'ses') {
                $message_id = optional(optional($event->message
                    ->getHeaders())->get('X-SES-Message-ID'))->getValue();
            }else if (config('mail.driver') == 'mailgun') {
                $message_id = optional(optional($event->message
                    ->getHeaders())->get('X-Mailgun-Message-ID'))->getValue();
            }

            resolve(EmailLogService::class)
                ->setAttr('tracker_id', $event->data['tracker_id'])
                ->store($campaign, $subscriber, $message->getBody(), $message_id);
        }
    }
}
