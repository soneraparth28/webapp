<?php


namespace App\Models\Core\App\Brand\Traits;


trait BrandUserBootTrait
{
    public static function boot()
    {
        parent::boot();

        if (!app()->runningInConsole()) {
            static::creating(function($model){
                return $model->fill([
                    'assigned_by' => auth()->id(),
                    'assigned_at' => now()
                ]);
            });
        }
    }
}
