<?php

use Faker\Generator as Faker;

$factory->define(App\Phone::class, function (Faker $faker) {
    return [
        'name' => $faker->company,
        'phone' => "0{$faker->numberBetween(100, 9999)} {$faker->numberBetween(100, 99999999)}",
    ];
});
