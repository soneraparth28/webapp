<?php


namespace App\Models\Lists\Traits;


use App\Models\Core\Traits\BrandRelationship;
use App\Models\Core\Traits\CreatedByRelationship;
use App\Models\Core\Traits\StatusRelationship;
use App\Models\Segment\Segment;
use App\Models\Subscriber\Subscriber;
use App\Repositories\App\StatusRepository;

trait ListRelationship
{

    use CreatedByRelationship,
        StatusRelationship,
        BrandRelationship;

    public function segments()
    {
        return $this->belongsToMany(
            Segment::class,
            'list_segment',
            'list_id',
            'segment_id'
        );
    }

    public function subscribers()
    {
        return $this->belongsToMany(
            Subscriber::class,
            'list_subscriber',
            'list_id',
            'subscriber_id'
        );
    }

    public function subscribed()
    {
        $statuses = resolve(StatusRepository::class)
            ->subscriber('status_subscribed');

        return $this->subscribers()
            ->whereIn('status_id', array_keys($statuses));
    }

    public function unsubscribed()
    {
        $statuses = resolve(StatusRepository::class)
            ->subscriber('status_unsubscribed');

        return $this->subscribers()
            ->whereIn('status_id', array_keys($statuses));
    }
}
