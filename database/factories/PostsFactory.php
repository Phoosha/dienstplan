<?php

use Faker\Generator as Faker;

$factory->define(App\Post::class, function (Faker $faker) {
    return [
        'user_id' => 1,
        'title' => $faker->text(50),
        'body' => $faker->paragraphs(3, true),
        'release_on' => $faker->dateTime('+1 hour'),
        'expire_on' => $faker->dateTimeBetween('tomorrow', 'next week'),
    ];
});
