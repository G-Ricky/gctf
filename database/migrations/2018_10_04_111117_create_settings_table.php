<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 128)->default('')->comment('配置名');
            $table->string('value', 2048)->default('')->comment('配置值');
            $table->string('type', 32)->nullable()->comment('配置类型');
            $table->string('description', 256)->default('')->comment('配置详细说明');
            $table->index('name');
            $table->unique('name');
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
        Schema::dropIfExists('settings');
    }
}
