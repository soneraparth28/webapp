<?php

namespace App\Notifications\Subscriber;

use App\Mail\Tag\SubscriberTag;
use App\Models\Core\App\Brand\Brand;
use App\Notifications\BaseNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;

class SubscriberNotification extends BaseNotification implements ShouldQueue
{
    use Queueable;

    public function __construct($templates, $via, $subscriber)
    {
        $this->templates = $templates;
        $this->via = $via;
        $this->model = $subscriber;
        $this->auth = auth()->user();
        $this->tag = SubscriberTag::class;
        if (in_array('mail', $via)) {
            $this->mailSettings = Brand::find($subscriber->brand_id)->mailSettings();
        }
        parent::__construct();
    }


    public function parseNotification()
    {
        $this->mailView = 'notification.mail.subscriber.index';
        $this->databaseNotificationUrl = route('tenant.subscribers.edit', [
            'subscriber' => optional($this->model)->id,
            'brand_dashboard' => optional($this->model)->brand_id
        ]);

        $this->mailSubject = $this->template()->mail()->parseSubject([
            '{name}' => optional($this->model)->full_name
        ]);

        $this->databaseNotificationContent = $this->template()->database()->parse([
            '{name}' => optional($this->model)->full_name
        ]);

        /*$this->nexmoNotificationContent = $this->template()->sms()->parse([
            '{first_name}' => optional($this->model)->first_name,
            '{last_name}' => optional($this->model)->last_name
        ]);*/
    }
}
