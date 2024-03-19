<?php


namespace App\Filters\Subscriber;


use App\Filters\Core\traits\BrandIdFilter;
use App\Filters\FilterBuilder;
use App\Filters\Traits\DateRangeFilter;
use App\Filters\Traits\StatusFilter;
use App\Models\Subscriber\Subscriber;
use Illuminate\Database\Eloquent\Builder;

class SubscriberFilter extends FilterBuilder
{
    use BrandIdFilter, DateRangeFilter, StatusFilter;

    public function search($value = null)
    {
        $this->groupSearch(
            $value,
            ['email'],
            function (Builder $builder) use ($value) {
                $builder->orWhereRaw(
                    'CONCAT(first_name," ", last_name) LIKE  ?', "%$value%"
                );
            }
        );
    }

    public function limit($limit = null)
    {
        $this->builder->when($limit, function (Builder $builder) use ($limit) {
            $builder->limit($limit);
        });
    }

    public function email($email = null)
    {
        $this->builder->when($email, function (Builder $builder) use ($email) {
            $builder->where('email', 'LIKE', "%$email%");
        });
    }

    public function include($include = null)
    {
        $this->builder->when($include, function (Builder $builder) use ($include) {
            $ids = explode(',', $include);
            $builder->union(Subscriber::select(['id', 'email'])->whereIn('id', $ids));
        });
    }

    public function lists($id = null)
    {
        if ($id) {
            $this->builder->whereHas('lists', function (Builder $builder) use($id) {
                $builder->where('list_id', $id);
            });
        }
    }

    public function selected($ids = null)
    {
        $this->builder->when($ids, function (Builder $builder) use ($ids) {
            $ids = explode(',', $ids);
            $builder->union(Subscriber::select(['id', 'email', 'first_name', 'last_name'])->whereIn('id', $ids));
        });
    }

}
