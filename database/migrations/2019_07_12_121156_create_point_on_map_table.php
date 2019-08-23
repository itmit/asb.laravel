<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePointOnMapTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Schema::dropIfExists('point_on_map');
        Schema::create('point_on_map', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('client')->unsigned()->nullable();
            $table->double('latitude');
            $table->double('longitude');
            $table->timestamps();

            $table->foreign('client')->references('id')->on('clients')
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
        Schema::dropIfExists('point_on_map');
    }
}
