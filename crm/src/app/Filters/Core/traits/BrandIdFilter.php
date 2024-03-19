<?php


namespace App\Filters\Core\traits;


trait BrandIdFilter
{
    public function brandId($brand_id = null)
    {
        $this->whereClause('brand_id', $brand_id, $this->brandIdOperator);
    }
}
