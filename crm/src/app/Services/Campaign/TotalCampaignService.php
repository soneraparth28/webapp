<?php

namespace App\Services\Campaign;
use App\Models\Campaign\Campaign;
use App\Services\AppService;

class TotalCampaignService extends AppService
{

	public function __construct(Campaign $campaign)
	{
		$this->model = $campaign;
	}

}
