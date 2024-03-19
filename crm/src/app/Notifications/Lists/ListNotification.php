<?php

namespace App\Notifications\Lists;

use App\Mail\Tag\ListTag;
use App\Models\Core\App\Brand\Brand;
use App\Notifications\BaseNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;

class ListNotification extends BaseNotification implements ShouldQueue
{
    use Queueable;

    public function __construct($templates, $via, $lists)
    {
        $this->templates = $templates;
        $this->via = $via;
        $this->model = $lists;
        $this->auth = auth()->user();
        $this->tag = ListTag::class;
        if (in_array('mail', $via)) {
            $this->mailSettings = Brand::find($lists->brand_id)->mailSettings();
        }
        parent::__construct();
    }

    public function parseNotification()
    {
        $this->mailView = 'notification.mail.lists.index';
        $this->databaseNotificationUrl = route('tenant.list.show-page', [
            'id' => $this->model->id,
            'brand_dashboard' => $this->model->brand_id
        ]);

        $this->mailSubject = $this->template()->mail()->parseSubject([
            '{name}' => $this->model->name
        ]);

        $this->databaseNotificationContent = $this->template()->database()->parse([
            '{name}' => $this->model->name
        ]);

        /*$this->nexmoNotificationContent = $this->template()->sms()->parse([
            '{name}' => $this->model->name
        ]);*/
    }
}
