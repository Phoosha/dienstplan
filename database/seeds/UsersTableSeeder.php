<?php

use App\User;
use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder {

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        User::create([
            'first_name' => 'Max',
            'last_name' => 'Mustermann',
            'login' => 'mmuster',
            'email' => 'max@mustermail.de',
            'phone' => '0151 72429',
            'password' => '$2y$10$TKh8H1.PfQx37YgCzwiKb.KjNyWgaHb9cbcoQgdIVFlYg7B77UdFm', // secret
            'api_token' => str_random(60),
        ]);
        User::create([
            'first_name' => 'Erika',
            'last_name' => 'Mustermann',
            'login' => 'emuster',
            'email' => 'erika@mustermail.de',
            'phone' => '0176 97423',
            'password' => '$2y$10$TKh8H1.PfQx37YgCzwiKb.KjNyWgaHb9cbcoQgdIVFlYg7B77UdFm', // secret
            'is_admin' => true,
        ]);
        factory(App\User::class, 10)->create();
    }

}
