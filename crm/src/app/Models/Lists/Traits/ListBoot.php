<?php


namespace App\Models\Lists\Traits;



use App\Repositories\App\StatusRepository;
use App\Scopes\BrandScope;

trait ListBoot
{
    public static function boot()
    {
        parent::boot();
        static::addGlobalScope(new BrandScope());
        if (!app()->runningInConsole()){
            static::creating(function($model){
                return $model->fill([
                    'created_by' => auth()->id(),
                    'type' => $model->type ? $model->type : 'imported',
                    'status_id' => resolve(StatusRepository::class)->listImported()
                ]);
            });
        }
    }
}
