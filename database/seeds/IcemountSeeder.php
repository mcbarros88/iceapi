<?php

use Illuminate\Database\Seeder;

class IcemountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('icecasts')->insert([

        'admin-user'=>'seed',
        'admin-password' => 'seed',
        'admin-mail' => 'seed@seed.it',
        'port' => '8000',
        'mount-name' => 'seeder',
        'password' => 'seed',
        'max-listeners' => '10',
        'bitrate' => '64',
        ]);
    }
}
