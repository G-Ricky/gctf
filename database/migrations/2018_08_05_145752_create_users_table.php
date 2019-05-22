<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id')->comment('用户id');
            $table->string('sid', 32)->unique()->nullable()->default(null)->comment('学号');
            $table->string('username', 32)->unique()->comment('登录名');
            $table->string('nickname', 32)->unique()->nullable()->default(null)->comment('昵称');
            $table->string('name', 32)->default('')->comment('姓名');
            $table->enum('gender', ['UNKNOWN', 'MALE', 'FEMALE'])->default('UNKNOWN')->comment('性别');
            $table->string('email', 255)->nullable()->default(null)->comment('邮箱');
            $table->string('password', 255)->comment('密码(Hash)');
            $table->rememberToken();
            $table->softDeletes();
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
        Schema::dropIfExists('users');
    }
}
