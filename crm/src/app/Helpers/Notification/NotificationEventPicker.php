<?php


namespace App\Helpers\Notification;


use App\Models\Core\Auth\User;
use App\Models\Core\Setting\NotificationEvent;
use App\Models\Core\Setting\NotificationSetting;
use Illuminate\Database\Eloquent\Builder;

class NotificationEventPicker
{
    public function on($event)
    {
        $event = NotificationEvent::with('templates')
            ->where('name', $event)
            ->first();

        if (!$event)
            return ['audiences' => [], 'templates' => collect([]), 'via' => []];

        $settings = NotificationSetting::with('audiences')->where('notification_event_id', $event->id)
            ->when(request()->brand_id, function (Builder $builder) {
                $builder->where('brand_id', brand()->id);
            }, function (Builder $builder) {
                $builder->whereNull('brand_id');
            })
            ->get();

        $settings = $settings->reduce(function ($pre, NotificationSetting $setting) {
            return [
                'audiences' => array_merge(isset($pre['audiences']) ? $pre['audiences'] : [], optional($setting)->users()),
                'via' => array_merge(isset($pre['via']) ? $pre['via'] : [], optional($setting)->notifyBy)
            ];
        }, []);

        $audiences = !empty($settings['audiences']) ? array_unique($settings['audiences']) : [];

        $via = !empty($settings['via']) ? array_unique($settings['via']) : [];

        if (!config('notification.notify_notifier')) {
            $audiences = array_filter($audiences, function ($audience) {
                return $audience != auth()->id();
            });
        }

        if (count($audiences) && request()->brand_id) {
            $audiences = User::whereIn('id', $audiences)->whereHas('roles', function (Builder $builder) {
                $builder->where('brand_id', brand()->id)
                    ->orWhereNull('brand_id');
            })->get();
        }

        return ['audiences' => $audiences, 'templates' => optional($event)->templates, 'via' => $via];
    }
}
