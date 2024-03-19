<?php


namespace App\Filters\Traits;


use Illuminate\Database\Eloquent\Builder;

trait LimitFilterTrait
{
    public function limit($limit = null)
    {
        $this->builder->when($limit, function (Builder $builder) use ($limit) {
            $builder->limit($limit);
        });
    }
}
