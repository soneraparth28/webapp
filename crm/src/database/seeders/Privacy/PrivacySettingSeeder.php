<?php
namespace Database\Seeders\Privacy;

use App\Services\Core\Setting\SettingService;
use Illuminate\Database\Seeder;

class PrivacySettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $context = config('settings.brand_default_prefix')['privacy'];

        $array = [
            'track_open_in_campaigns' => 1,
            'track_clicks_in_your_campaigns' => 1,
            'track_location_in_your_campaigns' => 1
        ];

        resolve(SettingService::class)
            ->saveSettings($array, $context);
    }
}
