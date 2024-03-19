<?php


namespace App\Services\Campaign;


use App\Helpers\Traits\NumberHelper;
use App\Repositories\App\StatusRepository;
use App\Services\AppService;

class CampaignEmailLogService extends AppService
{
    use NumberHelper;
    public function rates($type)
    {
        $counter = "{$type}EmailLogs";
        $count = $this->model->$counter()->count();
        $status_id = resolve(StatusRepository::class)->emailDelivered();
        
        $totalDeliveredCount = $this->model->emailLogs()
            ->where('status_id', $status_id)
            ->count();

        if (!$totalDeliveredCount) {
            return "N/A";
        }

        return $this->numberFormatter(($count/$totalDeliveredCount) * 100) . "%";
    }
}
