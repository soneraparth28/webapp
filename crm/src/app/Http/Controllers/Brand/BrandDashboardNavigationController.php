<?php

namespace App\Http\Controllers\Brand;

use App\Exceptions\GeneralException;
use App\Http\Controllers\Controller;

class BrandDashboardNavigationController extends Controller
{
    public function campaigns()
    {
        if (auth()->user()->can('view_campaigns')){
            return view('brands.campaign.index');
        }

        throw new GeneralException(trans('default.action_not_allowed'));
    }

    public function emails()
    {
        if (auth()->user()->can('view_emails')){
            return view('brands.emails.index');
        }

        throw new GeneralException(trans('default.action_not_allowed'));
    }

    public function list()
    {
        if (auth()->user()->can('view_lists')){
            return view('brands.list.index');
        }
        throw new GeneralException(trans('default.action_not_allowed'));
    }

    public function listView($id)
    {
        return view('brands.list.view', compact('id'));
    }

    public function segments()
    {
        if (auth()->user()->can('view_segments')){
            return view('brands.segments.index');
        }

        throw new GeneralException(trans('default.action_not_allowed'));
    }

    public function subscribers()
    {
        if (auth()->user()->can('view_subscribers')) {
            return view('brands.subscribers.index');
        }

        throw new GeneralException(trans('default.action_not_allowed'));
    }

    public function blockedSubscribers()
    {
        if (auth()->user()->can('view_subscribers')){
            return view('brands.subscribers.blacklisted');
        }

        throw new GeneralException(trans('default.action_not_allowed'));
    }

    public function templatesCardView()
    {
        if (auth()->user()->can('view_templates')){
            return view('brands.templates.card', ['view' => 'card']);
        }

        throw new GeneralException(trans('default.action_not_allowed'));
    }

    public function templatesListView()
    {
        if (auth()->user()->can('view_templates')){
            return view('brands.templates.index', ['view' => 'list']);
        }

        throw new GeneralException(trans('default.action_not_allowed'));
    }

    public function settings()
    {
        if (authorize_any(['view_delivery_settings', 'view_custom_fields', 'view_notification_settings', 'generate_subscriber_api_url' ])){
            return view('brands.settings.index', ['permissions' => [
                'custom_field' => auth()->user()->can('view_custom_fields'),
                'delivery_settings' => auth()->user()->can('view_specific_brand_delivery_settings'),
                'notification' => authorize_any(['view_notification_settings', 'update_notification_settings']),
                'subscriber_api' => auth()->user()->can('generate_subscriber_api_url')
            ]]);
        }

        throw new GeneralException(trans('default.action_not_allowed'));
    }

}
