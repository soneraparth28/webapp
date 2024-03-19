<?php


namespace App\Filters\Traits;


trait StatusFilter
{
    public function status($ids = null)
    {
        if ($ids) $this->whereInClause('status_id', explode(',', $ids));
    }
}
