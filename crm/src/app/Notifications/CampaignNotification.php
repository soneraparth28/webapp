<?php

namespace App\Notifications;

use App\Mail\Tag\CampaignTag;
use App\Models\Core\App\Brand\Brand;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;

class CampaignNotification extends BaseNotification implements ShouldQueue
{
    use Queueable;

    public function __construct($templates, $via, $campaign)
    {
        $this->templates = $templates;
        $this->via = $via;
        $this->model = $campaign;
        $this->auth = auth()->user();
        $this->tag = CampaignTag::class;
        if (in_array('mail', $via)) {
            $this->mailSettings = Brand::find($campaign->brand_id)->mailSettings();
        }
        parent::__construct();
    }

    public function parseNotification()
    {
        $this->mailView = 'notification.mail.campaign.index';
        $this->databaseNotificationUrl = route('tenant.campaigns.view', [
            'campaign' => $this->model->id,
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
