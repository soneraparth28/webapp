<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;

class SettingApiController extends Controller
{
    public function configs()
    {
        return [
            'time_format' => config('settings.time_format'),
            'currency_position' => config('settings.currency_position'),
            'date_format' => config('settings.date_format'),
            'decimal_separator' => config('settings.decimal_separator'),
            'thousand_separator' => config('settings.thousand_separator'),
            'number_of_decimal' => config('settings.number_of_decimal'),
            'time_zones' => timezone_identifiers_list(),
            'mail_context' => array_keys(config('settings.supported_mail_services')),
            'brand_default_prefix_mail' => array_map(function ($mail) {
                return [
                    'id' => config('settings.brand_default_prefix.'.$mail),
                    'value' => trans('default.'.$mail)
                ];
            }, array_keys(config('settings.supported_mail_services'))),
            'use_for' => [
                'notification',
                'campaign',
                'test'
            ],
            'context' => config('settings.context')
        ];
    }
}
