<?php

namespace App\Models\Form;

use App\Models\Core\Builder\Form\CustomField as CoreCustomField;

class CustomField extends CoreCustomField
{
    protected $fillable = [
        'name', 'context', 'meta', 'in_list', 'is_for_admin', 'custom_field_type_id', 'created_by', 'brand_id'
    ];
}
