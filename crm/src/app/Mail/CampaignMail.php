<?php

namespace App\Mail;

use App\Helpers\Core\Traits\FileHandler;
use App\Helpers\Traits\InteractsWithTemplate;
use App\Models\Campaign\Campaign;
use App\Models\Core\File\File;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Swift_Message;

class CampaignMail extends Mailable
{
    use FileHandler, InteractsWithTemplate;

    public int $tries = 3;
    public int $timeout = 30;

    use Queueable, SerializesModels;

    public $last;
    public string $tracker_id;

    public Campaign $campaign;

    public object $subscriber;

    public function __construct(Campaign $campaign, object $subscriber, $last = false)
    {
        $this->campaign = $campaign;
        $this->subscriber = $subscriber;
        $this->last = $last;
    }

    public function build()
    {
        $this->updateCampaignStatus();

        $mail = $this->view('mail.campaign.campaign', [
            'html' => $this->getTemplate()
        ])->subject($this->campaign->subject);

        if (config('mail.driver') == 'ses') {
            $this->withSwiftMessage(function (Swift_Message $message) {
                $message->getHeaders()
                    ->addTextHeader('X-SES-CONFIGURATION-SET', config('services.ses.configuration_set'));
            });
        }


        if (optional($this->campaign->attachments)->count()) {
            $this->campaign->attachments->each(function (File $file) use ($mail) {
                $mail->attachFromStorage($this->removeStorage('public/'.$file->path));
            });
        }

        return $mail;
    }

    public function getTemplate(): string
    {
        $logo = config()->get('settings.application.company_logo');
        $img = asset(empty($logo) ? '/images/logo.png' : $logo);

        $vars = [
            '{app_name}' => config('app.name'),
            '{app_logo}' => "<img src='$img'>",
            '{subscriber_name}' => optional($this->subscriber)->full_name,
            '{subscriber_email}' => optional($this->subscriber)->email,
            '{brand_name}' => optional(optional($this->campaign)->brand)->name,
            '{campaign_name}' => $this->campaign->name,
            '{unsubscribe_link}' => $this->subscriber->unsubscribe_url,
            '{app_link}' => url('/')
        ];

        $body = "<meta charset='UTF-8'>".$this->campaign
            ->parseContent($vars);

        if (config('mail.driver') == 'smtp') {
            $url = route('webhook.smtp', ['hook' => 'opened', 'tracker_id' => $this->tracker_id]);
            $body .= "<img src='$url' style='visibility:hidden;'>";

            return $this->template($body)
                ->bypassAnchors(fn ($item) => route('webhook.smtp', [
                    'hook' => 'clicked',
                    'tracker_id' => $this->tracker_id,
                    'to' => $item->getAttribute('href')
                ]))->get();
        }

        return $body;
    }

    public function updateCampaignStatus(): bool
    {
        if (!$this->is_log_disabled() && $this->is_last()) {
//            notify()
//                ->on('campaign_sent')
//                ->with($this->campaign)
//                ->send(CampaignNotification::class);
//            resolve(CampaignStatusService::class)
//                ->update($this->campaign, 'status_sent');
            $sent = resolve(\App\Repositories\App\StatusRepository::class)->getStatusId('campaign', 'status_sent');
            $this->campaign->update(['status_id' => $sent]);
        }
        return true;
    }

    private function is_log_disabled(): bool
    {
        return $this->last === 'disable-log';
    }

    private function is_last(): bool
    {
        return $this->last === true;
    }

    public function setTrackerId(string $tracker_id): CampaignMail
    {
        $this->tracker_id = $tracker_id;

        return $this;
    }

}
