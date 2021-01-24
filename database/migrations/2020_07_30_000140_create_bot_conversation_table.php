<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBotConversationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bot_conversation', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('user_id')->nullable();
            $table->bigInteger('chat_id')->nullable();
            $table->enum('status', ['active','cancelled','stopped'])->default('active');
            $table->string('command')->default('');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('user_id');
            $table->index('chat_id');
            $table->index('status');

            $table->foreign('user_id')->references('id')->on('bot_user')->onDelete('cascade');
            $table->foreign('chat_id')->references('id')->on('bot_chat')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bot_conversation');
    }
}
