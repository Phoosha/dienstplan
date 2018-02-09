<?php

use App\SlotConfig;
use App\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder {

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        User::create([
            'first_name' => 'Admin',
            'last_name' => 'Istrator',
            'login' => 'admin',
            'email' => 'admin@example.org',
            'phone' => '0176 917423',
            'password' => '$2y$10$TKh8H1.PfQx37YgCzwiKb.KjNyWgaHb9cbcoQgdIVFlYg7B77UdFm', // secret
            'is_admin' => true,
        ]);
        SlotConfig::createWithSlots(
            Carbon::instance(config('dienstplan.min_date')),
            [ '79/1', '10/1' ]
        );
    }
}
