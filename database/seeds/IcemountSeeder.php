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

            'admin_user'=>'seed',
            'admin_password' => 'seed',
            'admin_mail' => 'seed@seed.it',
            'port' => '8000',
        ]);

        DB::table('mountpoints')->insert([

            'mount_name' => 'seeder',
            'password' => 'seed',
            'max_listeners' => '100',
            'bitrate' => '64',
            'icecast_id' => '1'
        ]);
    }
}
