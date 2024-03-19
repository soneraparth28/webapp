<?php


namespace App\Filters\Lists;


use App\Filters\Core\traits\AllowedResourceFilter;
use App\Filters\Core\traits\BrandIdFilter;
use App\Filters\Core\traits\NameFilter;
use App\Filters\FilterBuilder;
use App\Filters\Traits\DateRangeFilter;
use App\Filters\Traits\SearchFilterTrait;
use Illuminate\Database\Eloquent\Builder;

class ListFilter extends FilterBuilder
{
    use BrandIdFilter, AllowedResourceFilter, DateRangeFilter, NameFilter, SearchFilterTrait;

    public function withSegment($with_segment = null)
    {
        $this->builder->when($with_segment == 1, function (Builder $builder) {
            $builder->has('segments');
        }, function (Builder $builder) use ($with_segment) {
            if ($with_segment == 2){
                $builder->doesntHave('segments');
            }
        });
    }

    public function type($type = null)
    {
        $this->builder->when($type, function (Builder $builder) use ($type) {
            $builder->where('type', $type);
        });
    }
}
