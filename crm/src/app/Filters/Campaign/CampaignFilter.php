<?php


namespace App\Filters\Campaign;


use App\Filters\Core\traits\AllowedResourceFilter;
use App\Filters\Core\traits\BrandIdFilter;
use App\Filters\Core\traits\NameFilter;
use App\Filters\FilterBuilder;
use App\Filters\Traits\DateRangeFilter;
use App\Filters\Traits\SearchFilterTrait;
use App\Filters\Traits\StatusFilter;
use Illuminate\Database\Eloquent\Builder;

class CampaignFilter extends FilterBuilder
{
    use NameFilter, BrandIdFilter, StatusFilter, DateRangeFilter, AllowedResourceFilter, SearchFilterTrait;

    public function timePeriod($time_period = null)
    {
        $this->whereClause('time_period', $time_period);
    }

    public function archived($archived = null)
    {
        $this->builder->when($archived == "true", function (Builder $builder) {
            $builder->where('current_status', 'archived');
        });
    }

}
