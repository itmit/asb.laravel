<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateClientTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('client');
        Schema::create('client', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('representative')->unsigned();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->binary('user_picture');
            $table->string('phone_number');
            $table->string('note');
            $table->timestamps();

            $table->foreign('representative')->references('id')->on('users')
                ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('client');
    }
}
