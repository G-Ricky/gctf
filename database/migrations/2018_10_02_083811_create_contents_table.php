<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateContentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contents', function (Blueprint $table) {
            $table->increments('id')->comment('通知id');
            $table->string('title', 64)->default('')->comment('标题');
            $table->string('content', 2048)->default('')->comment('内容');
            $table->string('type', 16)->comment('类型');
            $table->unsignedInteger('poster')->default(0)->comment('发布人');
            $table->unsignedInteger('modifier')->default(0)->comment('最后修改人');
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
        Schema::dropIfExists('contents');
    }
}
