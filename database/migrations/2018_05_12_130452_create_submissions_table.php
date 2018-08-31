<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSubmissionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('submissions', function (Blueprint $table) {
            $table->increments('id')->comment('提交id');
            $table->unsignedInteger('challenge')->comment('题目id');
            $table->unsignedInteger('submitter')->comment('提交者');
            $table->string('content', 256)->comment('提交内容');
            $table->boolean('is_correct')->default(0)->comment('是否正确');
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
        Schema::dropIfExists('submissions');
    }
}
