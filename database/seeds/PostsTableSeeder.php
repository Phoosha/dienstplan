<?php

use Illuminate\Database\Seeder;

class PostsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('posts')->insert([
            'title' => 'Nummer EINS',
            'body' => '<b>Achtung!</b> Das ist eine wichtige Ankündigiung'
        ]);
    }
}
