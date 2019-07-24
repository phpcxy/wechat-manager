<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWechatMenuTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('admin_wechat_menu', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title');
            $table->unsignedInteger('parent_id')->default(0);
            $table->unsignedInteger('order')->default(0);
            $table->string('type', 16)->nullable();
            $table->string('key', 16)->nullable();
            $table->text('value')->nullable();
            $table->timestamps();
        });

        Schema::create('admin_wechat_reply', function (Blueprint $table) {
            $table->increments('id');
            $table->string('key');
            $table->text('value');
            $table->string('source', 16);
            $table->string('type', 16);
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
        Schema::dropIfExists('admin_wechat_menu');
        Schema::dropIfExists('admin_wechat_reply');
    }
}
