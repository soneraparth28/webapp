<?php


namespace App\Filters;


use App\Filters\Traits\CollectionHelper;
use App\Helpers\Core\Traits\Helpers;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;

class CollectionFilter
{
    use CollectionHelper, Helpers;
    protected $collection;
    public static $query;

    public function __construct(Collection $collection)
    {
        $this->collection = $collection;
    }

    public function trigger()
    {
        return $this->getCollection();
    }


    public function search(array $keys, $term)
    {
        $this->collection = $this->collectionIfHas($term, function ($rows) use($keys, $term) {
            return $this->filterTerm($rows, $keys, $term);
        });

        return $this;
    }

    public function within($key, $values)
    {
        $this->collection = $this->collectionIfHas($values, function ($rows) use($key, $values) {
            return $rows->whereIn($key, $values);
        });

        return $this;
    }

    public function dateBetween($column, $range)
    {
        $this->collection = $this->collectionIfHas($range, function ($rows) use($column, $range) {
            return $rows->filter(function($row)use($column, $range) {
                return $this->inDateBetween($row->{$column}, $range);
            });
        });
        return $this;
    }

    private function filterTerm($collection, $keys, $term, $relation = null)
    {
        return $collection->filter(function($row) use ($keys, $term, $relation) {
            return $this->whereLike($row, $keys, $term, $relation);
        });
    }

    private function stringSearch($key, $term)
    {
        return strpos( strtolower($key), strtolower($term) ) !== false;
    }


    public function whereHasLike(array $params, string $term = null)
    {
        $this->collection = $this->collectionIfHas($term, function ($rows) use ($params, $term)
        {
            $newCollection = collect();
            foreach ($params as $relation => $attributes) {
                $keys = $this->checkMakeArray($attributes);
                $filtered = $this->filterTerm($rows, $keys, $term, $relation);
                $newCollection->push($filtered);
            }
            return $newCollection->flatten();
        });


        return $this;
    }

    // depth search only for one to one relationships
    private function whereLike($model, $keys, $term, string $relation = null)
    {
        $flag = null;

        foreach ($keys as $key) {
            $key = $this->getKey($model, $key, $relation);
            $flag |= $this->stringSearch($key, $term);
        }
        return (bool)$flag;
    }

    private function getKey($model, $key, $relation = null)
    {
        if ($relation)
            return $model->{$relation}->{$key};

        return $model->{$key};
    }


    public function paginate($perPage = 10, $page = null, $baseUrl = null, $options = [])
    {
        $page = $page ?: (Paginator::resolveCurrentPage() ?: 1);

        $items = $this->collection;

        $lap = new LengthAwarePaginator(
            $items->forPage($page, $perPage)->values(),
            $items->count(),
            $perPage,
            $page,
            $options
        );

        $lap->setPath($baseUrl ?? request()->url());

        $this->collection = $lap;
        return $this;
    }

    public function __call($method, $arguments)
    {
        return $this->collection->{$method}($arguments);
    }


    public function getCollection()
    {
        return $this->collection;
    }


    public function setCollection(Collection $collection)
    {
        $this->collection = $collection;
        return $this;
    }

    public function collectionIfHas($term, callable $callback)
    {
        return $this->collection->when($term, function ($collection) use($callback) {
            return $callback($collection);
        },
        function () {
            return $this->collection;
        });
    }
}
