<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBotTelegramUpdateTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bot_telegram_update', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('chat_id')->nullable();
            $table->unsignedBigInteger('message_id')->nullable();
            $table->unsignedBigInteger('edited_message_id')->nullable();
            $table->unsignedBigInteger('channel_post_id')->nullable();
            $table->unsignedBigInteger('edited_channel_post_id')->nullable();
            $table->unsignedBigInteger('inline_query_id')->nullable();
            $table->unsignedBigInteger('chosen_inline_result_id')->nullable();
            $table->unsignedBigInteger('callback_query_id')->nullable();
            $table->unsignedBigInteger('shipping_query_id')->nullable();
            $table->unsignedBigInteger('pre_checkout_query_id')->nullable();
            $table->unsignedBigInteger('poll_id')->nullable();
            $table->unsignedBigInteger('poll_answer_poll_id')->nullable();
            $table->timestamps();

            $table->index('message_id');
            $table->index('edited_message_id');
            $table->index('channel_post_id');
            $table->index('edited_channel_post_id');
            $table->index('inline_query_id');
            $table->index('chosen_inline_result_id');
            $table->index('callback_query_id');
            $table->index('shipping_query_id');
            $table->index('pre_checkout_query_id');
            $table->index('poll_id');
            $table->index('poll_answer_poll_id');

            $table->foreign('edited_message_id')->references('id')->on('bot_edited_message')->onDelete('cascade');
            $table->foreign('edited_channel_post_id')->references('id')->on('bot_edited_message')->onDelete('cascade');
            $table->foreign('inline_query_id')->references('id')->on('bot_inline_query')->onDelete('cascade');
            $table->foreign('chosen_inline_result_id')->references('id')->on('bot_chosen_inline_result')->onDelete('cascade');
            $table->foreign('callback_query_id')->references('id')->on('bot_callback_query')->onDelete('cascade');
            $table->foreign('shipping_query_id')->references('id')->on('bot_shipping_query')->onDelete('cascade');
            $table->foreign('pre_checkout_query_id')->references('id')->on('bot_pre_checkout_query')->onDelete('cascade');
            $table->foreign('poll_id')->references('id')->on('bot_poll')->onDelete('cascade');
            $table->foreign('poll_answer_poll_id')->references('poll_id')->on('bot_poll_answer')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bot_telegram_update');
    }
}
