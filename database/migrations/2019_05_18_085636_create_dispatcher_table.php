<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDispatcherTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Create table for associating roles to users (Many-to-Many)
        Schema::create('dispatcher', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('representative')->unsigned();

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
        Schema::dropIfExists('dispatcher');
    }
}
