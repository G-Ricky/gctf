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
            $table->string('sid', 10)->unique()->comment('学号');
            $table->string('name', 16)->default('')->comment('姓名');
            $table->string('nickname', 16)->unique()->comment('昵称');
            $table->enum('sex', ['UNKNOW', 'MALE', 'FEMALE'])->default('UNKNOW')->comment('性别');
            $table->string('email', 255)->nullable()->default(null)->comment('邮箱');
            $table->string('password', 255)->comment('密码(Hash)');
            $table->enum('role', ['USER', 'ADMIN', 'SUPER', 'GUEST'])->default('GUEST')->comment('角色');
            $table->boolean('is_hidden')->default(0)->comment('是否隐藏');
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
