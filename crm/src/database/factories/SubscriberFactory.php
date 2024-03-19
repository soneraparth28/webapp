<?php

use App\Models\Core\App\Brand\Brand;
use App\Models\Core\Auth\User;
use App\Models\Core\Status;
use Faker\Generator as Faker;
use App\Models\Subscriber\Subscriber;
use Illuminate\Support\Carbon;

$factory->define(Subscriber::class, function (Faker $faker) {
    return [
        'first_name' => $faker->firstName,
        'last_name' => $faker->lastName,
        'email' => $faker->unique()->safeEmail,
        'brand_id' => Brand::query()
            ->inRandomOrder()
            ->first()->id,
        'status_id' => Status::query()
            ->where('type', 'subscriber')
            ->inRandomOrder()
            ->first()
            ->id,
        'created_by' => User::query()
        ->inRandomOrder()
        ->first()
        ->id,
        'created_at' => Carbon::parse($faker->dateTimeBetween($startDate = '-5 years', $endDate = '+1 weeks')),
        'updated_at' => Carbon::parse($faker->dateTimeBetween($startDate = '-4 years', $endDate = '+1 weeks')),
    ];
});
