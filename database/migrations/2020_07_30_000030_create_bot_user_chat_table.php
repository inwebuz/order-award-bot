<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBotUserChatTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bot_user_chat', function (Blueprint $table) {
            $table->bigInteger('user_id')->nullable();
            $table->bigInteger('chat_id')->nullable();
            $table->timestamps();

            $table->primary(['user_id', 'chat_id']);
            $table->foreign('user_id')->references('id')->on('bot_user')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('chat_id')->references('id')->on('bot_chat')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bot_user_chat');
    }
}
