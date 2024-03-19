<?php


namespace App\Services\Settings;


use App\Helpers\Core\Traits\HasWhen;
use App\Models\Form\CustomField;
use App\Models\Subscriber\Subscriber;
use App\Services\Core\Builder\Form\CustomFieldService;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class BrandCustomFieldService extends CustomFieldService
{
    private Collection $customFieldMetas;
    private Collection $dateTypeCustomFieldIds;
    private Collection $customFields;
    private Subscriber $subscriber;

    use HasWhen;

    public function __construct(CustomField $field)
    {
        parent::__construct($field);
    }

    public function setSubscriber($subscriber)
    {
        $this->subscriber = $subscriber;
        return $this;
    }


    public function customFields($brand_id, $context = 'subscriber')
    {
        return $this->model->query()
            ->with('customFieldType:id,name')
            ->where('context', $context)
            ->where(fn (Builder $builder) =>
            $builder->whereNull('brand_id')
                ->orWhere('brand_id', $brand_id)
            );
    }

    public function setCurrentCustomFields($brand_id = null)
    {
        $customFields = $this->customFields($brand_id)->get();

        $this->customFieldMetas = $customFields
            ->filter(fn ($field) => $field->meta)
            ->map(fn ($field) => [
                'id' => $field->id,
                'meta' => explode(',', $field->meta)
            ])
            ->pluck('meta', 'id');

        $this->dateTypeCustomFieldIds = $customFields->filter(function ($field) {
            return $field->customFieldType->name == 'date';
        })->pluck('id');

        $this->customFields = $customFields->pluck('id', 'name');

        return $this;
    }

    public function updateCustomFields($fields, callable $callback = null, $forceCheck = false)
    {
        $brand_id = $this->getAttr('brand_id');

        $this->setCurrentCustomFields($brand_id)->when(
            $callback,
            fn (BrandCustomFieldService $service) => $service->prepareCustomFields(
                $fields,
                fn ($id, $value) => $callback($id, $value),
                $forceCheck
            ),
            fn (BrandCustomFieldService $service) =>  $service->prepareCustomFields(
                $fields,
                $this->storeCustomFields()
            )
        );
    }


    public function prepareCustomFields($fields, \Closure $callback, $forceCheck = false)
    {
        foreach ($fields as $name => $value) {
            if ($this->customFields->keys()->contains($name) && $value) {

                $customFieldId = $this->customFields[$name];

                if ($this->dateTypeCustomFieldIds && $this->dateTypeCustomFieldIds->contains($customFieldId)) {
                    try {
                        $value = Carbon::parse($value)->format('Y-m-d');
                    } catch (\Exception $e) { continue; }
                }

                if ($forceCheck && $this->forceMetaCheck($value, $customFieldId)) {
                    continue;
                }

                $callback($customFieldId, $value, $name);
            }
        }
    }

    private function forceMetaCheck($value, $customFieldId)
    {
        return isset($this->customFieldMetas[$customFieldId])
            && !in_array(trim($value), $this->customFieldMetas[$customFieldId]);
    }


    public function storeCustomFields(Subscriber $subscriber = null)
    {
        $subscriber = $subscriber ?: $this->subscriber;

        return fn ($id, $value) => $subscriber->customFields()->create([
            'custom_field_id' => $id,
            'value' => $value
        ]);
    }
}
