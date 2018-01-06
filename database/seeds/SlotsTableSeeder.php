<?php

use App\SlotConfig;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class SlotsTableSeeder extends Seeder {

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        SlotConfig::createWithSlots(Carbon::parse('last year'), [ '79/1', '10/1' ]);
    }

}
