<?php


namespace App\Services\SMTP;


use App\Helpers\Core\Traits\InstanceCreator;
use App\Managers\Quota\QuotaManager;
use App\Services\AppService;
use Illuminate\Cache\RateLimiting\Limit;

class SMTPQuotaService extends AppService
{
    use InstanceCreator;

    public  function limit() : Limit
    {
        ['monthly' => $monthly, 'daily' => $daily, 'hourly' => $hourly] = $this->getAttrs('monthly', 'daily', 'hourly');
        return (new QuotaManager)
            ->setQuotas([
                'monthly' => $monthly - ($monthly * 0.10),
                'daily' => $daily,
                'hourly' => $hourly
            ])
            ->decideSuperiorLimiter();
    }


}

