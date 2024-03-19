<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Email\EmailLog;
use Faker\Generator as Faker;

$factory->define(EmailLog::class, function (Faker $faker) {
    $created = $faker->dateTimeBetween($startDate = '-4 years', $endDate = '+1 weeks');
    return [
        'email_date' => $created,
        'email_content' => '',
        'open_count' => 0,
        'click_count' => 0,
        'delivery_server' => $this->faker->randomElement(['amazon_ses', 'mailgun']),
        'created_at' => $created,
        'updated_at' => $faker->dateTimeBetween($startDate = '-4 years', $endDate = '+1 weeks'),
    ];
});
