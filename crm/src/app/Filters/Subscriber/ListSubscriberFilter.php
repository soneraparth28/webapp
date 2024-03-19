<?php


namespace App\Filters\Subscriber;

use App\Filters\CollectionFilter;

class ListSubscriberFilter extends CollectionFilter
{

    public function init()
    {
        $term = request('search', '');
        $dates = [];
        if ($range = json_decode(request('date'), true))
            $dates =  array_values($range);
        $statuses = request('status') ? explode(',', request('status'), true) : [];


        return $this->dateBetween('created_at', $dates)
            ->search(['full_name', 'email'], $term)
            ->within('status_id', $statuses)
            ->paginate(request('per_page', 10))
            ->trigger();
    }
}
