<?php


namespace App\Models\Campaign\Traits;


use App\Models\Campaign\CampaignAudience;
use App\Models\Campaign\CampaignActivity;
use App\Models\Core\File\File;
use App\Models\Core\Traits\BrandRelationship;
use App\Models\Core\Traits\CreatedByRelationship;
use App\Models\Core\Traits\StatusRelationship;
use App\Models\Email\EmailLog;
use Illuminate\Database\Eloquent\Builder;

trait CampaignRelationship
{
    use StatusRelationship,
        CreatedByRelationship,
        BrandRelationship;


    public function audiences()
    {
        return $this->hasMany(CampaignAudience::class);
    }

    public function attachments()
    {
        return $this->morphMany(File::class, 'fileable')
            ->where('type', 'attachment');
    }

    public function thumbnail()
    {
        return $this->morphOne(File::class, 'fileable')
            ->whereType('campaign_template');
    }

    public function emailLogs()
    {
        return $this->hasMany(EmailLog::class, 'campaign_id');
    }

    public function clickedEmailLogs()
    {
        return $this->emailLogs()
            ->where('click_count', '<>', 0);
    }

    public function openedEmailLogs()
    {
        return $this->emailLogs()
            ->where('open_count', '<>', 0);
    }

    public function lastActivity()
    {
        return $this->hasOne(CampaignActivity::class)
            ->orderBy('id', 'DESC')
            ->limit(1);
    }

    public function campaignActivities()
    {
        return $this->hasMany(CampaignActivity::class);
    }

    public function succeedEmailLogs()
    {
        return $this->emailLogs()->whereHas('status', function (Builder $builder) {
            $builder->whereIn('name', [
                'status_sent', 'status_delivered', 'status_open', 'status_clicked', 'status_new'
            ]);
        });
    }
}
