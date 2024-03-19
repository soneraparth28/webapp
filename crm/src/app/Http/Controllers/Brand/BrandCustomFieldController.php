<?php

namespace App\Http\Controllers\Brand;

use App\Filters\Core\CustomFieldFilter;
use App\Hooks\Form\BeforeCustomFieldSaved;
use App\Hooks\Form\CustomFieldCreated;
use App\Hooks\Form\WhileCustomFieldDeleting;
use App\Http\Controllers\Core\Builder\Form\CustomFieldController;
use App\Http\Requests\Core\Builder\Form\CustomFieldRequest;
use App\Models\Core\Builder\Form\CustomField;
use App\Services\Settings\BrandCustomFieldService;

class BrandCustomFieldController extends CustomFieldController
{
    public function __construct(BrandCustomFieldService $service, CustomFieldFilter $filter, CustomFieldCreated $hook)
    {
        parent::__construct($service, $filter, $hook);
    }

    public function index()
    {
        return $this->service
            ->select('custom_field_type_id', 'id', 'name', 'context', 'meta')
            ->with('customFieldType')
            ->where('brand_id', \request()->brand_id)
            ->orderBy('id', 'DESC')
            ->filters($this->filter)
            ->paginate(
                request('per_page', 15)
            );
    }

    public function store(CustomFieldRequest $request)
    {
        BeforeCustomFieldSaved::new(true)
            ->handle();

        $this->service
            ->save(
                $request->only('name', 'context', 'meta', 'in_list', 'custom_field_type_id', 'brand_id')
            );

        return created_responses('custom_field');
    }

    public function update(CustomField $customField, CustomFieldRequest $request)
    {
        BeforeCustomFieldSaved::new(true)
            ->handle();

        $customField->update(
            $request->only('name', 'context', 'meta', 'in_list', 'custom_field_type_id', 'brand_id')
        );

        return updated_responses('custom_field');
    }

    public function destroy(CustomField $customField)
    {
        WhileCustomFieldDeleting::new(true)
            ->setModel($customField)
            ->handle();

        if ($customField->delete()) {
            return deleted_responses('custom_field');
        }

        return failed_responses();
    }
}
