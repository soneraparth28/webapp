<?php


namespace App\Filters\Notification;


use App\Filters\Core\NotificationSettingFilter as NotificationSettingBaseFilter;
use App\Filters\Core\traits\BrandIdFilter;

class NotificationSettingFilter extends NotificationSettingBaseFilter
{
    use BrandIdFilter;
}
