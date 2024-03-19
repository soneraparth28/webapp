<?php


namespace App\Filters\Segment;


use App\Filters\Core\traits\BrandIdFilter;
use App\Filters\Core\traits\NameFilter;
use App\Filters\FilterBuilder;
use App\Filters\Traits\DateRangeFilter;
use App\Filters\Traits\SearchFilterTrait;
use Illuminate\Database\Eloquent\Builder;

class SegmentFilter extends FilterBuilder
{
    use BrandIdFilter, NameFilter, SearchFilterTrait, DateRangeFilter;

    public function rules($range = null)
    {
        $json = json_decode(htmlspecialchars_decode($range), true);

        $this->builder->when(count($json), function (Builder $builder) use ($json) {
            $builder->whereRaw('JSON_LENGTH(`segment_logic`) >= ? AND JSON_LENGTH(`segment_logic`) <= ?', [$json['min'], $json['max']]);
        });
    }
}
