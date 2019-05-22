<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateChallengesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('challenges', function (Blueprint $table) {
            $table->increments('id')->comment('题目id');
            $table->string('title', 32)->comment('标题');
            $table->string('description', 1024)->comment('描述');
            $table->enum('category', ['CRYPTO', 'MISC', 'PWN', 'REVERSE', 'WEB'])->default('MISC')->comment('类型名');
            $table->unsignedInteger('poster')->comment('出题人');
            $table->unsignedInteger('basic_points')->comment('基础分数');
            $table->unsignedInteger('points')->comment('分数');
            $table->string('flag', 256)->nullable()->comment('flag');
            $table->unsignedInteger('bank')->comment('题库id');
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
        Schema::dropIfExists('challenges');
    }
}
