<?php


namespace App\Mail\Tag;


class ListTag extends Tag
{
    protected $list;

    public function __construct($list, $notifier, $receiver)
    {
        $this->list = $list;
        $this->notifier = $notifier;
        $this->receiver = $receiver;
        $this->resourceurl = route('tenant.lists.view', [
            'lists' => $this->list->id,
            'brand_dashboard' => $this->list->brand_id
        ]);
    }

    function notification()
    {
        return array_merge([
            '{name}' => $this->list->name,
        ], $this->common());
    }
}
