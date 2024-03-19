<?php


namespace App\Models\Campaign\Traits;


trait CampaignAttribute
{
    public function getListsAttribute()
    {
        return $this->lists();
    }
}
