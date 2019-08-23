<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDispatcherTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Schema::dropIfExists('dispatcher');
        // Create table for associating roles to users (Many-to-Many)
        Schema::create('dispatcher', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('representative')->unsigned();
            $table->integer('user')->unsigned();

            $table->timestamps();

            $table->foreign('representative')->references('id')->on('users')
                ->onUpdate('cascade');
            $table->foreign('user')->references('id')->on('users')
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
        Schema::dropIfExists('dispatcher');
    }

}
