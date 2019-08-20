<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClientTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('clients');
        Schema::create('clients', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('representative')->unsigned();
            
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->rememberToken();
            
            $table->string('user_picture')->nullable();
            $table->string('phone_number')->unique();
            $table->string('organization')->nullable();
            $table->double('latitude')->nullable();
            $table->double('longitude')->nullable();
            $table->string('note')->nullable();
            $table->boolean('is_guard')->default(0);
            $table->boolean('is_active')->default(1);
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
        Schema::dropIfExists('clients');
    }
}
