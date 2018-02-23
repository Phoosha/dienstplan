<?php

use App\SlotConfig;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class SlotsTableSeeder extends Seeder {

    const START = "last year January 1st";

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        SlotConfig::createWithSlots(
            Carbon::parse(self::START),
            [ '79/1', '10/1' ]
        );
    }

}
