<?php

use App\Duty;
use App\User;
use Illuminate\Database\Seeder;

class DutiesTableSeeder extends Seeder {

    const MAX_TRIES = 1000;

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        $onceNoConflictSave = function (callable $modifier = null) {
            return function (Duty $duty) use ($modifier) {
                if (isset($modifier))
                    call_user_func($modifier, $duty);

                for ($i = 0; $duty->getConflicts()->isNotEmpty(); $i++) {
                    if ($i === self::MAX_TRIES)
                        return;

                    $duty = factory(Duty::class)->make();
                    if (isset($modifier))
                        call_user_func($modifier, $duty);
                }

                $duty->save();
            };
        };

        factory(Duty::class, 200)->make()->each($onceNoConflictSave());

        factory(Duty::class, 20)->make()
            ->each($onceNoConflictSave(function (Duty $duty) {
                $duty->user_id = User::where('is_admin', true)
                    ->inRandomOrder()->first()->id;
                $duty->type = Duty::SERVICE;
            }));

        factory(Duty::class, 50)->make()
            ->each($onceNoConflictSave(function (Duty $duty) {
                $duty->type = Duty::WITH_INTERNEE;
            }));
    }

}
