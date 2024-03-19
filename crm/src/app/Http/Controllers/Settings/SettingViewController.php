<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SettingViewController extends Controller
{
    public function appSettings()
    {
        return view('application.views.settings.app.index', ['permissions' => [
            'general' => authorize_any(['view_settings', 'update_settings']),
            'delivery' => authorize_any(['update_delivery_settings', 'view_delivery_settings']),
            'notification_template' => authorize_any(['view_notification_templates', 'create_notification_templates']),
            'notification' => authorize_any(['view_notification_settings', 'update_notification_settings']),
            'corn_job' => authorize_any(['update_corn_job_settings', 'view_corn_job_settings']),
            'update' => authorize_any(['check_for_updates', 'update_app'])
        ]]);
    }


    public function brandSettings()
    {
        return view('application.views.settings.brand.index', ['permissions' => [
            'delivery' => authorize_any(['view_brand_delivery_settings']),
            'privacy' => authorize_any(['view_brand_privacy_settings']),
            'templates' => authorize_any(['view_templates']),
            'custom_fields' => authorize_any(['view_custom_fields']),
            'notification' => authorize_any(['view_notification_settings'])
        ]]);
    }

    public function templateSettings()
    {
        return view('application.views.settings.template.index', ['permissions' => [
            'templates' => authorize_any(['view_templates']),
        ]]);
    }
}
