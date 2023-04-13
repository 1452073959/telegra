<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTelegramUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('telegram_user', function (Blueprint $table) {
            $table->increments('id');
            $table->string('user_no')->default('')->comment('用户编号');
            $table->string('user_name')->default('')->comment('用户名称');
            $table->dateTime('add_time')->comment('加入时间');
            $table->enum('user_status')->default('1')->comment('用户状态1,在群组2,不在群组');
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
        Schema::dropIfExists('telegram_user');
    }
}
