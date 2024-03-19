<?php


namespace App\Http\Controllers\Settings;


use App\Http\Controllers\Controller;
use App\Managers\Quota\QuotaManager;
use App\Services\Settings\SettingsService;
use function Symfony\Component\String\b;

class DeliveryQuotaController extends Controller
{
    public function show()
    {
        $settings = $this->getMailSettings();

        if (!$settings) {
            return [];
        }

        if ($settings['provider'] != 'smtp') {
            return [];
        }

        $quotas = QuotaManager::new()
            ->setQuotas([
                'hourly' => $settings['smtp_hourly_quota'],
                'daily' => $settings['smtp_daily_quota'],
                'monthly' => $settings['smtp_monthly_quota'],
            ])->get();

        if (!$quotas) {
            return [];
        }

        if ($quotas->hourly) {
            return ['type' => 'hourly', 'count' => $quotas->hourly];
        }

        if ($quotas->daily) {
            return ['type' => 'daily', 'count' => $quotas->daily];
        }

        return ['type' => 'monthly', 'count' => $quotas->monthly];
    }

    private function getMailSettings()
    {
        if (!brand()) {
            return resolve(SettingsService::class)
            ->getMailSettings();
        }
        if (!brand()->defaultDeliverySettings) {
            return [];
        }
        return brand()->mailSettings();
    }
}