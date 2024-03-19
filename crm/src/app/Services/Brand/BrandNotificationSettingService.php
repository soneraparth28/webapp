<?php


namespace App\Services\Brand;


use App\Models\Core\App\Brand\Brand;
use App\Models\Core\Setting\NotificationEvent;
use App\Models\Core\Setting\NotificationSetting;
use App\Services\AppService;

class BrandNotificationSettingService extends AppService
{
    public function migrate(Brand $brand, $brandTypeId, $role)
    {
        $channels = ['database'];

        NotificationEvent::where('type_id', $brandTypeId)
            ->pluck('id')
            ->each(function ($event) use ($channels, $role, $brand) {
                    $notification_setting = NotificationSetting::query()->create([
                        'notification_event_id' => $event,
                        'notify_by' => $channels,
                        'brand_id' => $brand->id
                    ]);

                    $notification_setting->audiences()->create([
                        'audience_type' => 'roles',
                        'audiences' => [$role->id]
                    ]);
            });
    }
}
