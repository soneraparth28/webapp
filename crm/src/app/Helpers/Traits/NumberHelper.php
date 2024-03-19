<?php


namespace App\Helpers\Traits;


trait NumberHelper
{
    public function numberFormatter($number)
    {
        return number_format(
            $number,
            (strpos(strval($number), '.') > -1) ? config('settings.application.number_of_decimal') : 0,
            (strpos(strval($number), '.') > -1) ? config('settings.application.decimal_separator') : '',
            config('settings.application.thousand_separator')
        );
    }
}
