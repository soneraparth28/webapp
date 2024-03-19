<?php

use App\Models\Template\Template;
use Faker\Generator as Faker;

$factory->define(Template::class, function (Faker $faker) {
    return [
        'subject' => $faker->sentence,
        'default_content' => $faker->randomHtml('4', '4')
    ];
});
