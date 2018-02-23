<?php

use App\User;
use Carbon\Carbon;
use Faker\Generator as Faker;

$factory->define(App\Post::class, function (Faker $faker) {
    return [
        'user_id' => function () {
            return User::where('is_admin', true)
                ->inRandomOrder()->first()->id;
        },
        'title' => $faker->text(50),
        'body' => $faker->paragraphs(3, true),
        'release_on' => Carbon::instance(
            $faker->dateTimeBetween('-1 hour', '+1 hour')
        )->startOfDay(),
        'expire_on' => Carbon::instance(
            $faker->dateTimeBetween('tomorrow', 'next week')
        )->startOfDay(),
    ];
});
