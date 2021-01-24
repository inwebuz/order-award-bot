<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBotPollTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bot_poll', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('question')->nullable();
            $table->text('options')->nullable();
            $table->unsignedBigInteger('total_voter_count')->default(0);
            $table->tinyInteger('is_closed')->default(0);
            $table->tinyInteger('is_anonymous')->default(1);
            $table->string('type')->nullable();
            $table->tinyInteger('allows_multiple_answers')->default(0);
            $table->unsignedBigInteger('correct_option_id')->default(0);
            $table->string('explanation')->nullable();
            $table->text('explanation_entities')->nullable();
            $table->unsignedBigInteger('open_period')->nullable();
            $table->timestamp('close_date')->nullable();
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
        Schema::dropIfExists('bot_poll');
    }
}
