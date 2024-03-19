<?php


namespace App\Filters\Traits;


use Illuminate\Database\Eloquent\Builder;

trait UserFilterTrait
{
    public function typeFilter($type = null)
    {
        return $this->builder->when($type, function (Builder $builder) use ($type) {
            $builder->when($type == 'app', function (Builder $builder) {
                $builder->whereDoesntHave('brands');
            }, function (Builder $builder) {
                $builder->whereHas('roles', function (Builder $builder) {
                    $builder->where('id', brand()->id);
                });
            });
        });
    }
}
