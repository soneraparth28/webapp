<?php


namespace App\Jobs\Middleware;


use App\Jobs\Mail\CampaignMailSender;
use Illuminate\Queue\Middleware\RateLimited;

class AppRateLimited extends RateLimited
{

    protected function handleJob($job, $next, array $limits)
    {
        foreach ($limits as $limit) {
            if ($this->limiter->tooManyAttempts($limit->key, $limit->maxAttempts)) {
                return $this->shouldRelease
                    ? $job->release($this->getTimeUntilNextRetry($limit->key))
                    : $this->reQueue($job);
            }

            $this->limiter->hit($limit->key, $limit->decayMinutes * 60);
        }

        return $next($job);
    }

    private function reQueue(CampaignMailSender $job): bool
    {
        CampaignMailSender::dispatch(
            $job->campaign,
            $job->subscriber,
            $job->last
        )->delay(now()->addHour());

        return false;
    }
}
