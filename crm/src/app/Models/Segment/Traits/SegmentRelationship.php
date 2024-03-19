<?php


namespace App\Models\Segment\Traits;


use App\Models\Core\Traits\BrandRelationship;
use App\Models\Core\Traits\CreatedByRelationship;
use App\Models\Lists\Lists;

trait SegmentRelationship
{
    use CreatedByRelationship,
        BrandRelationship;


    public function lists()
    {
        return $this->belongsToMany(
            Lists::class,
            'list_segment',
            'list_id',
            'segment_id'
        );
    }
}
