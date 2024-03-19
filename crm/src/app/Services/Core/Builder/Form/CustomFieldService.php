<?php


namespace App\Services\Core\Builder\Form;


use App\Models\Core\Builder\Form\CustomField;
use App\Services\Core\BaseService;
use Illuminate\Database\Eloquent\Builder;

class CustomFieldService extends BaseService
{
    public function __construct(CustomField $field)
    {
        $this->model = $field;
    }

    public function subscriberFields()
    {
        return $this->model->where('context', 'subscriber')
            ->where(function (Builder $builder) {
                $builder->whereNull('brand_id')
                    ->orWhere('brand_id', request()->get('brand_id'));
            })
            ->pluck('name');
    }

}
