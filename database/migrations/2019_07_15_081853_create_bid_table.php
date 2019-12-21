<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBidTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Schema::dropIfExists('bid');
        Schema::create('bid', function (Blueprint $table) {
            $table->increments('id');
            $table->string('uid');
            $table->enum('type', ['Alert', 'Call']);
            $table->integer('guard')->unsigned()->nullable();

            $table->integer('client')->unsigned();
            $table->enum('status', ['Accepted', 'PendingAcceptance', 'Processed']);
            $table->double('latitude');
            $table->double('longitude');

            // $table->foreign('client')->references('id')->on('clients')
            //     ->onUpdate('cascade');

            // $table->foreign('guard')->references('id')->on('clients')
            //     ->onUpdate('cascade');

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
        Schema::dropIfExists('bid');
    }
}
