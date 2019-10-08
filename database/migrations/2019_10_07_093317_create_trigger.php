<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTrigger extends Migration
{
    // public function up()
    // {
    //     DB::unprepared('
    //     CREATE TRIGGER tr_User_Default_Member_Role AFTER INSERT ON `users` FOR EACH ROW
    //         BEGIN
    //             INSERT INTO role_user (`role_id`, `user_id`, `created_at`, `updated_at`) 
    //             VALUES (3, NEW.id, now(), null);
    //         END
    //     ');
    // }

    // public function down()
    // {
    //     DB::unprepared('DROP TRIGGER `tr_User_Default_Member_Role`');
    // }
}
