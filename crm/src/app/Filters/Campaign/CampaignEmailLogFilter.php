<?php


namespace App\Filters\Campaign;


use App\Filters\CollectionFilter;

class CampaignEmailLogFilter extends CollectionFilter
{
    public function init()
    {
        $term = request('search', '');
        $dates = [];
        if ($range = json_decode(request('date'), true))
            $dates =  array_values($range);
        $statuses = request('status') ? explode(',', request('status')) : [];
        
        return $this->whereHasLike([
                'subscriber' => 'email',
                'campaign' => 'name'
            ],  $term)
            ->dateBetween('email_date', $dates)
            ->within('status_id', $statuses)
            ->paginate(request('per_page', 10))
            ->trigger();
    }
}
