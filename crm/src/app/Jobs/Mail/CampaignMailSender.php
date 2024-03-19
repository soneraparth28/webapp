<?php

namespace App\Jobs\Mail;

use App\Config\SetMailConfig;
use App\Jobs\Middleware\AppRateLimited;
use App\Mail\CampaignMail;
use App\Models\Campaign\Campaign;
use App\Models\Subscriber\Subscriber;
use App\Services\SMTP\SMTPQuotaService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\URL;

class CampaignMailSender implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    public $last;

    public Campaign $campaign;
    public array $config = [];

    public $subscriber;

    public function __construct(Campaign $campaign, $subscriber, $last = false)
    {
        $this->campaign = $campaign;
        $this->subscriber = $this->buildSubscriberObject($subscriber);
        $this->last = $last;
    }

    public function handle()
    {
        Mail::to($this->subscriber->email)
            ->send(
                (new CampaignMail($this->campaign, $this->subscriber, $this->last))
                    ->setTrackerId($this->getTrackerId())
            );
    }

    private function setConfig() : array
    {
        $this->config = $this->campaign
            ->brand
            ->mailSettings();

        (new SetMailConfig( $this->config ))
            ->clear()
            ->set();

        return $this->config;
    }

    public function middleware() : array
    {
        $this->setConfig();
        if ($this->config['provider'] == 'smtp') {
            $this->setRateLimiter();
            return [
                (new AppRateLimited($this->config['smtp_user_name']))->dontRelease()
            ];
        }

        return [];
    }

    private function setRateLimiter()
    {
        RateLimiter::for($this->config['smtp_user_name'], function (CampaignMailSender $job) {
            return SMTPQuotaService::new(true)
                ->setAttrs([
                    'hourly' => $job->config['smtp_hourly_quota'],
                    'daily' => $job->config['smtp_daily_quota'],
                    'monthly' => $job->config['smtp_monthly_quota']
                ])
                ->limit()
                ->by($job->config['smtp_user_name']);
        });
    }

    public function buildSubscriberObject($subscriber): object
    {
        if ($subscriber instanceof \stdClass) {
            $subscriber = (array)$subscriber;
        }

        if ($subscriber instanceof Subscriber) {
            $subscriber = $subscriber->toArray();
        }

        $subscriber['unsubscribe_url'] = $this->buildUnsubscribeURL($subscriber);

        return (object)$subscriber;
    }

    private function buildUnsubscribeURL($subscriber): string
    {
        if ($this->last !== 'disable-log') {
            return URL::signedRoute('subscriber.unsubscribe',
                ['brand_id' => $this->campaign->brand_id, 'email' => $subscriber['email']]
            );
        }
        return '';
    }

    public function getTrackerId(): string
    {
        return $this->campaign->id ."-". optional($this->subscriber)->id . "-" . time() . "-" . uniqid();
    }
}
