<?php


namespace App\Models\Campaign\Traits;


use App\Builder\Campaign\SubscriberBuilder;
use App\Helpers\Traits\HasMemoization;
use App\Models\Lists\Lists;

trait CampaignMethod
{
    use HasMemoization;

    public function listAudiences()
    {
        $list = $this->audiences
            ->firstWhere('audience_type', 'list');
        return $list ? $list->audiences : [];
    }

    public function subscriberAudiences()
    {
        $subscriber = $this->audiences
            ->firstWhere('audience_type', 'subscriber');
        return optional($subscriber)->audiences ?? [];
    }

    public function lists()
    {
        return $this->memoize('campaign-list-'.$this->id, function () {
            return Lists::query()
                ->with('subscribers')
                ->findMany(
                    $this->listAudiences()
                );
        });
    }

    public function subscriberBuilder($statuses = [], $fields = [])
    {
        return (new SubscriberBuilder($this))
            ->whereStatus($statuses)
            ->select($fields)
            ->withSubscribers()
            ->build();
    }

    public function listSubscriberBuilder($statuses = [], $fields = [])
    {
        return (new SubscriberBuilder($this))
            ->whereStatus($statuses)
            ->select($fields)
            ->build();
    }

    public function parseContent(array $vars)
    {
        return strtr($this->template_content, $vars);
    }

}
