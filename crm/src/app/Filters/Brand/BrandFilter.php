<?php
/**
 * Created by PhpStorm.
 * User: emamu
 * Date: 4/20/2020
 * Time: 6:38 PM
 */

namespace App\Filters\Brand;


use App\Filters\Core\traits\NameFilter;
use App\Filters\Core\traits\StatusIdFilter;
use App\Filters\FilterBuilder;
use App\Filters\Traits\DateRangeFilter;
use Illuminate\Database\Eloquent\Builder;

class BrandFilter extends FilterBuilder
{
    use StatusIdFilter, NameFilter, DateRangeFilter;

    public function search($search = null)
    {
        $this->builder->when($search, function (Builder $builder) use ($search){
            $builder->where('name', 'LIKE', "$search%")
                ->orWhere('short_name', 'LIKE', "$search%");
        });
    }
    
}