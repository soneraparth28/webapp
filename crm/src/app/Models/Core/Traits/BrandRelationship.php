<?php


namespace App\Models\Core\Traits;


use App\Models\Core\App\Brand\Brand;

trait BrandRelationship
{
    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }
}
