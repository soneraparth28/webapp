<?php

namespace App\Http\Controllers\Brand;

use App\Filters\Core\NotificationEventFilter;
use App\Http\Controllers\Controller;
use App\Http\Requests\Notification\NotificationSettingRequest;
use App\Models\Core\Setting\NotificationEvent;
use App\Models\Core\Setting\NotificationSetting;
use App\Services\Core\Setting\NotificationSettingService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasOne;

class BrandNotificationEventController extends Controller
{
    public function __construct(NotificationSettingService $service, NotificationEventFilter $filter)
    {
        $this->service = $service;
        $this->filter = $filter;
    }

    public function index()
    {
        return NotificationEvent::with([
            'settings' => function(HasOne $builder) {
                $builder->where('brand_id', request()->brand_id);
            },'settings.audiences'
        ])->select('id', 'name', 'type_id')
            ->whereHas('type', function (Builder $query) {
                $query->where('alias', 'brand');
            })
            ->filters($this->filter)
            ->paginate(request('per_page', 10));
    }

    public function show(NotificationEvent $notificationEvent)
    {
        return $notificationEvent->load([
            'settings' => function(HasOne $builder) {
                $builder->where('brand_id', request()->brand_id);
            },'settings.audiences'
        ]);
    }

    public function update($notificationEvent, NotificationSettingRequest $request)
    {
        $settings = NotificationSetting::where('brand_id', brand()->id)
            ->where('notification_event_id', $notificationEvent)
            ->firstOrNew();

        $settings->fill([
            'notification_event_id' => $notificationEvent,
            'updated_by' => auth()->id(),
            'brand_id' => brand()->id,
            'notify_by' => $request->notify_by
        ]);

        $settings->save();

        $this->service->setModel($settings)->syncAudiences($request->audiences);

        return updated_responses('notification');
    }
}
