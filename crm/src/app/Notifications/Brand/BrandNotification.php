<?php

namespace App\Notifications\Brand;

use App\Mail\Tag\BrandTag;
use App\Notifications\BaseNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;

class BrandNotification extends BaseNotification implements ShouldQueue
{
    use Queueable;

    public function __construct($templates, $via, $brand)
    {
        $this->templates = $templates;
        $this->via = $via;
        $this->model = $brand;
        $this->auth = auth()->user();
        $this->tag = BrandTag::class;
        parent::__construct();
    }

    public function parseNotification()
    {
        $this->mailView = 'notification.mail.brand.index';
        $this->databaseNotificationUrl = route('brands.lists');

        $this->mailSubject = $this->template()->mail()->parseSubject([
            '{name}' => $this->model->name,
            '{short_name}' => $this->model->short_name
        ]);

        $this->databaseNotificationContent = $this->template()->database()->parse([
            '{name}' => $this->model->name,
            '{short_name}' => $this->model->short_name
        ]);

        /*$this->nexmoNotificationContent = $this->template()->sms()->parse([
            '{name}' => $this->model->full_name
        ]);*/

    }
}
