<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('inquiries', function (Blueprint $table) {
            $table->id();
            $table->integer('status')->default(1)->comment('状態');
            $table->unsignedBigInteger('user_id')->nullable()->comment('ユーザーID');
            $table->string('name')->nullable()->comment('名前');
            $table->string('email')->nullable()->comment('メールアドレス');
            $table->string('title')->comment('タイトル');
            $table->text('detail')->comment('詳細');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('inquiries');
    }
};
