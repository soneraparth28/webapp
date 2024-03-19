<?php


namespace App\Models\Campaign\Traits;


trait CampaignAudienceAttribute
{
    /**
     * @return mixed
     */
    public function getAudiencesAttribute()
    {
        return json_decode($this->attributes['audiences']);
    }

    public function setAudiencesAttribute($audiences)
    {
        $this->attributes['audiences'] = json_encode($audiences);
    }
}
