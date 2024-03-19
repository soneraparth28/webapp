<?php


namespace App\Models\Subscriber\Traits;


use App\Models\Core\Traits\BrandRelationship;
use App\Models\Core\Traits\CreatedByRelationship;
use App\Models\Core\Traits\CustomFieldRelationship as CustomFieldsRelationship;
use App\Models\Core\Traits\StatusRelationship;
use App\Models\Email\EmailLog;
use App\Models\Lists\Lists;
use App\Repositories\App\StatusRepository;
use Illuminate\Database\Eloquent\Builder;

trait SubscriberRelationship
{
    use BrandRelationship,
        StatusRelationship,
        CreatedByRelationship,
        CustomFieldsRelationship;

    public function lists()
    {
        return $this->belongsToMany(
            Lists::class,
            'list_subscriber',
            'subscriber_id',
            'list_id'
        );
    }

    public function emailLogs()
    {
        return $this->hasMany(EmailLog::class, 'subscriber_id');
    }

    public function delivered()
    {
        return $this->emailLogs()
            ->whereIn('status_id', resolve(StatusRepository::class)->emailDeliveredStatus());
    }

    public function sent()
    {
        return $this->emailLogs()
            ->whereIn('status_id', resolve(StatusRepository::class)->emailSentStatus());
    }

    public function lastActivity()
    {
        return $this->hasOne(EmailLog::class, 'subscriber_id')
            ->latest('updated_at');
    }

    public function succeedEmailLogs()
    {
        return $this->emailLogs()
            ->whereIn('status_id', resolve(StatusRepository::class)->emailSucceedStatus());
    }

}
