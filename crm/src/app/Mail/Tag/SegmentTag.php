<?php


namespace App\Mail\Tag;


class SegmentTag extends Tag
{
    protected $segment;

    public function __construct($segment, $notifier, $receiver)
    {
        $this->segment = $segment;
        $this->notifier = $notifier;
        $this->receiver = $receiver;
        $this->resourceurl = route('tenant.segments.edit', [
            'segment' => optional($this->segment)->id,
            'brand_dashboard' => optional($this->segment)->brand_id
        ]);
    }

    function notification()
    {
        return array_merge([
            '{name}' => $this->segment->name,
        ], $this->common());
    }
}
