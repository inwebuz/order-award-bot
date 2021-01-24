<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBotCallbackQueryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bot_callback_query', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('user_id')->nullable();
            $table->bigInteger('chat_id')->nullable();
            $table->unsignedBigInteger('message_id')->nullable();
            $table->string('inline_message_id')->nullable();
            $table->string('chat_instance')->default('');
            $table->string('data')->default('');
            $table->string('game_short_name')->default('');
            $table->timestamps();

            $table->index(['user_id', 'chat_id', 'message_id']);
            $table->foreign('user_id')->references('id')->on('bot_user')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bot_callback_query');
    }
}
