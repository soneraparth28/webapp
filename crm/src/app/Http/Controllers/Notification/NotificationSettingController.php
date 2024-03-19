<?php

namespace App\Http\Controllers\Notification;

use App\Exceptions\GeneralException;
use App\Filters\Notification\NotificationSettingFilter;
use App\Http\Controllers\Core\Setting\NotificationSettingController as NotificationSettingCoreController;
use App\Models\Core\Setting\NotificationAudience;
use App\Services\Core\Setting\NotificationSettingService;

class NotificationSettingController extends NotificationSettingCoreController
{

    public function __construct(NotificationSettingService $service, NotificationSettingFilter $filter)
    {
        parent::__construct($service, $filter);
    }

    public function showSettings($notification_event)
    {
        if (auth()->user()->can('view_notification_settings')){
            $settings = $this->service
                ->with('audiences')
                ->filters($this->filter)
                ->where('notification_event_id', $notification_event)
                ->first();
            optional($settings->audiences)->each(function (NotificationAudience $audience) {
                $audience->setRelation('users', $audience->users());
                $audience->setRelation('roles', $audience->roles());
            });
            return $settings;
        }
        throw new GeneralException(trans('default.action_not_allowed'));
    }
}
