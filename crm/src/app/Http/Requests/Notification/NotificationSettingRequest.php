<?php

namespace App\Http\Requests\Notification;

use App\Helpers\Traits\BrandInactiveTrait;
use App\Http\Requests\Core\Setting\NotificationSettingRequest as BaseNotificationSettingRequest;

class NotificationSettingRequest extends BaseNotificationSettingRequest
{
    use BrandInactiveTrait;
    public function authorize()
    {
        return $this->actionIfInactive();
    }
}
