<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBotEditedMessageTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bot_edited_message', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('chat_id')->nullable();
            $table->unsignedBigInteger('message_id')->nullable();
            $table->bigInteger('user_id')->nullable();
            $table->timestamp('edit_date')->nullable();
            $table->text('text')->nullable();
            $table->text('entities')->nullable();
            $table->text('caption')->nullable();
            $table->timestamps();

            $table->index(['chat_id', 'message_id', 'user_id']);
            $table->foreign('chat_id')->references('id')->on('bot_chat')->onDelete('cascade');
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
        Schema::dropIfExists('bot_edited_message');
    }
}
