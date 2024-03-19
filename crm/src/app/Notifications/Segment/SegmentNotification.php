<?php

namespace App\Notifications\Segment;

use App\Mail\Tag\SegmentTag;
use App\Models\Core\App\Brand\Brand;
use App\Notifications\BaseNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;

class SegmentNotification extends BaseNotification implements ShouldQueue
{
    use Queueable;


    public function __construct($templates, $via, $segment)
    {
        $this->templates = $templates;
        $this->via = $via;
        $this->model = $segment;
        $this->auth = auth()->user();
        $this->tag = SegmentTag::class;
        if (in_array('mail', $via)) {
            $this->mailSettings = Brand::find($segment->brand_id)->mailSettings();
        }
        parent::__construct();
    }

    public function parseNotification()
    {
        $this->mailView = 'notification.mail.segment.index';
        $this->databaseNotificationUrl = route('tenant.segments.edit', [
            'segment' => optional($this->model)->id,
            'brand_dashboard' => optional($this->model)->brand_id
        ]);

        $this->mailSubject = $this->template()->mail()->parseSubject([
            '{name}' => optional($this->model)->name
        ]);

        $this->databaseNotificationContent = $this->template()->database()->parse([
            '{name}' => optional($this->model)->name
        ]);

        /*$this->nexmoNotificationContent = $this->template()->sms()->parse([
            '{name}' => optional($this->model)->name
        ]);*/
    }
}
