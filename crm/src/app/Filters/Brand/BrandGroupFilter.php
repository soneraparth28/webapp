<?php


namespace App\Filters\Brand;


use App\Filters\Core\traits\NameFilter;
use App\Filters\FilterBuilder;
use App\Filters\Traits\DateRangeFilter;
use App\Filters\Traits\SearchFilterTrait;

class BrandGroupFilter extends FilterBuilder
{
    use DateRangeFilter, NameFilter, SearchFilterTrait;
}
