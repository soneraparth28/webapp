<?php


namespace App\Services\Subscriber\Helpers;


use App\Helpers\Core\Traits\HasWhen;
use App\Repositories\Subscriber\SubscriberRepository;
use App\Services\AppService;

trait BulkActionTrait
{
    use HasWhen;
    public function prepareSubscribersAttrs($attrs)
    {
        $requested = is_array($attrs) ? (object)$attrs : $attrs;
        $statuses = ['subscribed', 'unsubscribed'];

        if (optional($requested)->pickOnly) {
            $statuses = is_array($requested->pickOnly) ? $requested->pickOnly : [$requested->pickOnly];
        }


        return $this->when(
            !$requested->isBulkAction,
            fn (AppService $service) => $service
                ->setAttr('subscribers', $requested->subscribers),

            fn (AppService $service) => $service
                ->setAttr('subscribers', resolve(SubscriberRepository::class)
                    ->getBulkIds($statuses))
        );
    }
}
