<?php


namespace App\Models\Core\Traits;


trait MailRules
{

    protected $basicRules = [
            'from_email' => 'required|email',
            'from_name' => 'required|min:3',
    ];

    public function mailgunRules()
    {
        return array_merge([
            'domain_name' => 'required|min:3',
            'api_key' => 'required|min:3',
            'webhook_key' => 'required|min:3'
        ], $this->basicRules);
    }

    public function amazonSesRules()
    {
        return array_merge([
            'api_region' => 'required|min:3',
            'access_key_id' => 'required|min:3',
            'secret_access_key' => 'required|min:3',
        ], $this->basicRules);
    }

    public function smtpRules()
    {
        $rules =  array_merge([
            'smtp_host' => 'required|min:5',
            'smtp_port' => 'required|min:2',
            'smtp_encryption' => 'required',
            'smtp_user_name' => 'required|min:2',
            'smtp_password' => 'required|min:2',
            'smtp_hourly_quota' => 'required|numeric|min:0',
            'smtp_daily_quota' => 'required|numeric|min:0',
            'smtp_monthly_quota' => 'required|numeric',
        ], $this->basicRules);

        if (request()->smtp_daily_quota > 0) {
            $rules['smtp_daily_quota'] .= '|gt:smtp_hourly_quota';
        }
        if (request()->smtp_monthly_quota > 0 ) {
            $days = now()->daysInMonth + 20;
            $rules['smtp_monthly_quota'] .= "|min:$days|gt:smtp_daily_quota";
        }
        else {
            $rules['smtp_monthly_quota'] .= '|min:0';
        }

        return $rules;
    }
}
