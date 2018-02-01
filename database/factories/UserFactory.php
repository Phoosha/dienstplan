<?php

use Faker\Generator as Faker;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| This directory should contain each of the model factory definitions for
| your application. Factories provide a convenient way to generate new
| model instances for testing / seeding your application's database.
|
*/

$factory->define(App\User::class, function (Faker $faker) {
    return [
        'first_name' => $faker->firstName,
        'last_name' => $faker->lastName,
        'login' => $faker->unique()->userName,
        'email' => $faker->unique()->safeEmail,
        'phone' => "0{$faker->numberBetween(100, 9999)} {$faker->numberBetween(100, 99999999)}",
        'password' => '$2y$10$TKh8H1.PfQx37YgCzwiKb.KjNyWgaHb9cbcoQgdIVFlYg7B77UdFm', // secret
        'last_training' => $faker->dateTimeThisYear(),
    ];
});
