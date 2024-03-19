<?php


namespace App\Models\Core\App\Brand\Traits;


use App\Models\Core\App\Brand\Brand;
use App\Models\Core\Auth\User;

trait BrandGroupRelationship
{
    public function brands()
    {
        return $this->hasMany(Brand::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

}
