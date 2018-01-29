<?php

use App\Phone;
use Illuminate\Database\Seeder;

class PhonesTableSeeder extends Seeder {

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        factory(Phone::class, 5)->create();
    }

}
