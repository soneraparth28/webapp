<?php


namespace App\Models\Segment\Traits;


trait SegmentRules
{
    public function createdRules()
    {
        return [
            'name' => 'required|min:3',
            'segment_logic' => 'required|array|min:1',
        ];
    }
}
