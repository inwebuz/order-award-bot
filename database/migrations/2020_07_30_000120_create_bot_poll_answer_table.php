<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBotPollAnswerTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bot_poll_answer', function (Blueprint $table) {
            $table->unsignedBigInteger('poll_id');
            $table->bigInteger('user_id');
            $table->text('option_ids')->nullable();
            $table->timestamps();

            $table->primary(['poll_id', 'user_id']);
            $table->foreign('poll_id')->references('id')->on('bot_poll')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bot_poll_answer');
    }
}
