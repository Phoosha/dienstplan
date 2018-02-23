<?php

use App\Duty;
use App\SlotConfig;
use App\User;
use Carbon\Carbon;
use Faker\Generator as Faker;

$factory->define(App\Duty::class, function (Faker $faker) {
    $roundToHalfHours = function (DateTime $dt) {
        $dt = Carbon::instance($dt);

        $dt->second = 0;
        if ($dt->minute < 15) {
            $dt->minute = 0;
        } elseif ($dt->minute >= 45) {
            $dt->minute = 0;
            $dt->addHour();
        } else {
            $dt->minute = 30;
        }

        return $dt;
    };

    return [
        'uuid' => $faker->uuid,
        'user_id' => function () {
            return User::query()->inRandomOrder()->first()->id;
        },
        'start' => $roundToHalfHours(
            $faker->dateTimeBetween(
                SlotsTableSeeder::START,
                now()->add(config('dienstplan.duty.store_future'))
            )
        ),
        'end' => function (array $duty) use ($faker, $roundToHalfHours) {
            return $roundToHalfHours(
                Carbon::instance($duty['start'])->addMinutes(
                    $faker->biasedNumberBetween(60, 4 * 24 * 60,
                        // shift the bias towards the min value
                        function ($x) {
                            return -(2 * $x - 1)**2 + 1;
                        })
                )
            );
        },
        'type' => Duty::NORMAL,
        'slot_id' => function (array $duty) use ($faker) {
            return SlotConfig::active(Carbon::instance($duty['start']))
                ->slots->random()->id;
        }
    ];
});
