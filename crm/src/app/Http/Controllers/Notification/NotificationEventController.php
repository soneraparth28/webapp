<?php

namespace App\Http\Controllers\Notification;

use App\Http\Controllers\Core\Notification\NotificationEventController as BaseNotificationEventController;

class NotificationEventController extends BaseNotificationEventController
{
    public function index()
    {
        if (authorize_any(['view_notification_templates', 'view_notification_settings'])) {
            return parent::index();
        }
        return [];
    }
}
