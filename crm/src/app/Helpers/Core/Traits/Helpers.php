<?php


namespace App\Helpers\Core\Traits;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;

trait Helpers
{
    /**
     * @param $data
     * @return array
     */
    public function checkMakeArray($data = null): array
    {
        if ($data instanceof Collection) {
            return $data->toArray();
        }

        if(is_string($data)) $data = preg_split('/[,|]/', $data);

        return is_array($data)
            ? $data
            : [ $data ];
    }

    public function fillAssoc($keys, $values, $arrays = []): array
    {
        if ($keys instanceof Collection) $keys = $keys->toArray();

        return array_merge(
            array_fill_keys($keys, $values), $arrays
        );
    }
}
