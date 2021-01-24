<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBotChosenInlineResultTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bot_chosen_inline_result', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('result_id')->default('');
            $table->bigInteger('user_id')->nullable();
            $table->string('location')->nullable();
            $table->string('inline_message_id')->nullable();
            $table->text('query')->nullable();
            $table->timestamps();

            $table->index('user_id');
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
        Schema::dropIfExists('bot_chosen_inline_result');
    }
}
