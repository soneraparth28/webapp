<?php

/** @var Factory $factory */

use App\Models\Campaign\Campaign;
use App\Models\Core\App\Brand\Brand;
use App\Models\Core\Auth\User;
use App\Models\Core\Status;
use App\Models\Template\Template;
use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;

$factory->define(Campaign::class, function (Faker $faker) {
    $template = Template::inRandomOrder()->first();
    return [
        'name' => $faker->sentence($nbWords = 3, $variableNbWords = true),
        'subject' => $faker->realText($maxNbChars = 200, $indexSize = 2),
        'template_content' => $template->default_content,
        'time_period' => $faker->randomElement([
            'immediately', 'once', 'hourly', 'daily', 'weekly', 'monthly', 'yearly'
        ]),
        'start_at' => $faker->dateTimeBetween($startDate = 'now', $endDate = '+2 months'),
        'end_at' => $faker->dateTimeBetween($startDate = '+1 months', $endDate = '+4 months'),
        'campaign_start_time' => $faker->time('H:i'),

        'brand_id' => Brand::inRandomOrder()
            ->pluck('id')
            ->first(),
        'created_by' =>  User::inRandomOrder()
            ->pluck('id')
            ->first(),
        'status_id' => Status::where('type', 'campaign')
            ->where('name', '!=', 'status_draft')
            ->inRandomOrder()
            ->pluck('id')
            ->first()
    ];
});
