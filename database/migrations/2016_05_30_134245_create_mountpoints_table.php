<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMountpointsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mountpoints', function (Blueprint $table) {
            $table->increments('id');
            $table->string('mount-name');
            $table->integer('icecast_id');
            $table->string('password');
            $table->integer('max-listeners');
            $table->integer('bitrate');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('mountpoints');
    }
}
