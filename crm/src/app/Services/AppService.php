<?php


namespace App\Services;


use App\Services\Core\BaseService;
use Illuminate\Database\Eloquent\Model;

class AppService extends BaseService
{

    public function duplicate(Model $model)
    {
        $this->model = $model->replicate();
        $this->model->save();
        return $this->model;
    }


}

