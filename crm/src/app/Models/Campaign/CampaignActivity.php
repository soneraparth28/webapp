<?php

namespace App\Models\Campaign;

use Illuminate\Database\Eloquent\Model;

class CampaignActivity extends Model
{
    protected $table = "campaign_activities";

    public $timestamps = false;

    protected $fillable = ['processed_at', 'status_id', 'campaign_id'];

    public function campaign()
    {
        return $this->belongsTo(Campaign::class);
    }
}
