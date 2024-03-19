<?php


namespace App\Mail\Tag;


class SubscriberTag extends Tag
{

    protected $subscriber;

    public function __construct($subscriber, $notifier, $receiver)
    {
        $this->subscriber = $subscriber;
        $this->notifier = $notifier;
        $this->receiver = $receiver;
        $this->resourceurl = route('tenant.subscribers.edit', [
            'subscriber' => optional($this->subscriber)->id,
            'brand_dashboard' => optional($this->subscriber)->brand_id ?? optional($this->subscriber)->id
        ]);
    }

    function notification()
    {
        return array_merge([
            '{name}' => $this->subscriber->full_name
        ], $this->common());
    }

    public function import()
    {
        $this->resourceurl = route('tenant.subscribers.lists', [
            'brand_dashboard' => optional($this->subscriber)->id
        ]);

        return $this->common();
    }

    public function blackListed()
    {
        return $this->import();
    }
}
