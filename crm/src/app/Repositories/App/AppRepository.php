<?php


namespace App\Repositories\App;


use App\Models\AppModel;
use App\Repositories\Core\BaseRepository;
use Illuminate\Database\Eloquent\Model;

class AppRepository extends BaseRepository
{
    protected $builder;

    public function setModel(Model $model)
    {
        $this->model = $model;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getBuilder()
    {
        $builder = $this->builder;
        $this->unsetBuilder();
        return $builder;
    }

    public function setBuilder(AppModel $model)
    {
        $this->builder = $model::query();
        return $this;
    }

    private function unsetBuilder()
    {
        $this->builder = $this->model::query();
    }
}
