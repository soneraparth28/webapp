<?php


namespace App\Filters\Core;


use App\Filters\FilterBuilder;
use App\Filters\Traits\DateRangeFilter;
use Illuminate\Database\Eloquent\Builder;

class TemplateFIlter extends FilterBuilder
{
    use DateRangeFilter;
    public function search($search = null)
    {
        $this->builder->when($search, function (Builder $builder) use ($search) {
            $builder->where('subject', 'LIKE', "%{$search}%");
        });
    }
}
