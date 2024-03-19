<?php

/** @var Factory $factory */

use App\Models\Core\App\Brand\Brand;
use App\Models\Core\Auth\User;
use App\Models\Core\Status;
use App\Models\Lists\Lists;
use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;

$factory->define(Lists::class, function (Faker $faker) {
    return [
        'name' => $faker->sentence($nbWords = 3, $variableNbWords = true),
        'description' => $faker->realText($maxNbChars = 200, $indexSize = 2),
        'type' => $faker->randomElement(['imported', 'dynamic']),
        'brand_id' => Brand::inRandomOrder()
            ->pluck('id')
            ->first(),
        'created_by' =>  User::inRandomOrder()
            ->pluck('id')
            ->first(),
        'status_id' => Status::where('type', 'list')
            ->pluck('id')
            ->first()
    ];
});
