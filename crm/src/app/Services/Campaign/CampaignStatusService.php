<?php


namespace App\Services\Campaign;


use App\Models\Campaign\Campaign;
use App\Repositories\App\StatusRepository;
use App\Services\AppService;

class CampaignStatusService extends AppService
{
    protected $repostory;
    public function __construct(StatusRepository $repository)
    {
        $this->repostory = $repository;
    }

    public function update(Campaign $campaign, $name = 'status_processing')
    {
        $campaign->status_id = $this->getStatus($name);
        $campaign->save();
        return $campaign;
    }

    public function getStatus($name)
    {
        return $this->repostory
            ->getStatusId('campaign', $name);
    }

}
