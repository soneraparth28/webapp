<?php


namespace App\Repositories\CustomField;


use App\Models\Core\Builder\Form\CustomField;
use App\Repositories\App\AppRepository;
use Illuminate\Database\Eloquent\Builder;

class CustomFieldRepository extends AppRepository
{
    public function __construct(CustomField $customField)
    {
        $this->model = $customField;
    }

    public function fields($context, $in_list = false)
    {
        return $this->model::query()
            ->where(function (Builder $builder) {
                $builder->whereNull('brand_id')
                    ->orWhere('brand_id', request()->get('brand_id'));
            })
            ->where('context', $context)
            ->when(request()->in_list || $in_list, function (Builder $builder) {
                $builder->where('in_list', 1);
            })
            ->get(['id', 'name', 'context', 'meta', 'custom_field_type_id'])->map(function ($field) {
                return [
                    'id' => $field->id,
                    'name' => $field->name,
                    'meta' => $field->meta,
                    'type' => $field->customFieldType->name,
                ];
            });
    }
}
