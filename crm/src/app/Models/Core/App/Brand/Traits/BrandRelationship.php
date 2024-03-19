<?php


namespace App\Models\Core\App\Brand\Traits;


use App\Models\Campaign\Campaign;
use App\Models\Core\App\Brand\BrandUserPivot;
use App\Models\Core\Auth\User;
use App\Models\Core\Builder\Form\CustomFieldValue;
use App\Models\Core\Setting\Setting;
use App\Models\Core\Status;
use App\Models\Core\Traits\CreatedByRelationship;
use App\Models\Core\Traits\StatusRelationship;
use App\Models\Subscriber\Subscriber;

trait BrandRelationship
{
    use CreatedByRelationship, StatusRelationship;

    public function users()
    {
        return $this->belongsToMany(
            User::class,
            'brand_user',
            'brand_id',
            'user_id'
        )->withPivot('assigned_at', 'assigned_by')
            ->using(BrandUserPivot::class);
    }

    public function customFields()
    {
        return $this->morphMany(
            CustomFieldValue::class,
            'contextable'
        );
    }

    public function settings()
    {
        return $this->morphMany(Setting::class, 'settingable');
    }

    public function subscribers()
    {
        return $this->hasMany(Subscriber::class);
    }

    public function campaigns()
    {
        return $this->hasMany(Campaign::class);
    }

    public function defaultDeliverySettings()
    {
        return $this->morphOne(Setting::class, 'settingable')
            ->where('name', 'brand_own_default_delivery_settings');
    }

    public function deliverySettings()
    {
        return $this->morphMany(Setting::class, 'settingable')
            ->whereIn('context', [...array_keys(config('settings.supported_mail_services'))]);
    }

    public function brandDeliveryMailSettingNameEmail()
    {
        return $this->morphMany(Setting::class, 'settingable')
            ->where('context', 'brand_own_default_mail_email_name');
    }


}
