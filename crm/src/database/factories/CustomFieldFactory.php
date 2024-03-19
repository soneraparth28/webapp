<?php


use App\Models\Core\Builder\Form\CustomField;
use App\Models\Core\Builder\Form\CustomFieldType;
use Faker\Generator as Faker;

$factory->define(CustomField::class, function (Faker $faker) {
    $contexts = config('settings.context');
    return [
        'name' => $faker->name,
        'context' => $faker->randomElement(array_diff($contexts, ['subscriber'])),
        'custom_field_type_id' => CustomFieldType::query()->inRandomOrder()->first()->id,
        'created_by' => 1
    ];
});
