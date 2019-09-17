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
        // Schema::dropIfExists('clients');
        Schema::create('clients', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('representative')->unsigned();
            
            $table->string('name')->nullable()->default(NULL);; // ФИО ДЛЯ ФИЗ ЛИЦ
            $table->string('passport')->unique()->nullable(); // ПАСПОРТ ДЛЯ ФИЗ ЛИЦ
            $table->string('email')->unique()->nullable();
            $table->string('password');
            $table->rememberToken();
            
            $table->string('user_picture')->nullable()->default('storage/avatars/nophoto.png');
            $table->enum('type', ['Individual', 'Entity', 'Guard']); // ВЫБОР ТИПА КЛИЕНТА (ФИЗ ЛИЦО, ЮР ЛИЦО, ОХРАНА)
            $table->string('phone_number')->unique();
            $table->string('organization')->nullable()->default(NULL); // НАИМЕНОВАНИЕ ОРГАЗИНАЦИИ ДЛЯ ЮР ЛИЦ
            $table->string('INN')->nullable()->default(NULL); // ИНН ДЛЯ ЮР ЛИЦ
            $table->string('OGRN')->nullable()->default(NULL); // ОГРН ДЛЯ ЮР ЛИЦ
            $table->string('director')->nullable()->default(NULL); // ДИРЕКТОР ДЛЯ ЮР ЛИЦ
            $table->double('latitude')->nullable();
            $table->double('longitude')->nullable();
            $table->string('note')->nullable();
            $table->boolean('is_guard')->default(0);
            $table->timestamp('active_from')->nullable()->default(NULL);
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
