<?php

namespace App\Notifications\Template;

use App\Models\Core\App\Brand\Brand;
use App\Notifications\BaseNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;

class TemplateNotification extends BaseNotification implements ShouldQueue
{
    use Queueable;

    public function __construct($templates, $via, $template)
    {
        $this->templates = $templates;
        $this->via = $via;
        $this->model = $template;
        $this->auth = auth()->user();
        if (in_array('mail', $via)) {
            $this->mailSettings = Brand::find($template->brand_id)->mailSettings();
        }
        parent::__construct();
    }

    public function parseNotification()
    {
        $this->mailView = 'notification.mail.template.index';
        $this->databaseNotificationUrl = route('tenant.templates.edit', [
            'template' => optional($this->model)->id,
            'brand_dashboard' => optional($this->model)->brand_id
        ]);

        $this->mailSubject = $this->template()->mail()->parseSubject([
            '{subject}' => optional($this->model)->subject
        ]);

        $this->databaseNotificationContent = $this->template()->database()->parse([
            '{subject}' => optional($this->model)->subject
        ]);

        /*$this->nexmoNotificationContent = $this->template()->sms()->parse([
            '{subject}' => optional($this->model)->subject
        ]);*/
    }
}
