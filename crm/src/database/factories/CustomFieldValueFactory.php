<?php


use App\Models\Core\Builder\Form\CustomField;
use App\Models\Core\Builder\Form\CustomFieldValue;
use Faker\Generator as Faker;

$factory->define(CustomFieldValue::class, function (Faker $faker) {

    $customField = CustomField::query()
        ->where('context', 'subscriber')
        ->inRandomOrder()
        ->first();

    $value = $faker->firstName;
    if ($customField->meta) {
        $value = $faker->randomElement(explode(',', $customField->meta));
    }
    return [
        'value' => $value,
        'custom_field_id' => $customField->id,
        'updated_by' => 1,
    ];
});
