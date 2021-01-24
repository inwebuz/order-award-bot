<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBotMessageTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bot_message', function (Blueprint $table) {
            $table->bigInteger('chat_id')->default(0);
            $table->unsignedBigInteger('id')->default(0);
            $table->bigInteger('user_id')->nullable();
            $table->timestamp('date')->nullable();
            $table->bigInteger('forward_from')->nullable();
            $table->bigInteger('forward_from_chat')->nullable();
            $table->bigInteger('forward_from_message_id')->nullable();
            $table->text('forward_signature')->nullable();
            $table->text('forward_sender_name')->nullable();
            $table->timestamp('forward_date')->nullable();
            $table->bigInteger('reply_to_chat')->nullable();
            $table->unsignedBigInteger('reply_to_message')->nullable();
            $table->bigInteger('via_bot')->nullable();
            $table->unsignedBigInteger('edit_date')->nullable();
            $table->text('media_group_id')->nullable();
            $table->text('author_signature')->nullable();
            $table->text('text')->nullable();
            $table->text('entities')->nullable();
            $table->text('caption_entities')->nullable();
            $table->text('audio')->nullable();
            $table->text('document')->nullable();
            $table->text('animation')->nullable();
            $table->text('game')->nullable();
            $table->text('photo')->nullable();
            $table->text('sticker')->nullable();
            $table->text('video')->nullable();
            $table->text('voice')->nullable();
            $table->text('video_note')->nullable();
            $table->text('caption')->nullable();
            $table->text('contact')->nullable();
            $table->text('location')->nullable();
            $table->text('venue')->nullable();
            $table->text('poll')->nullable();
            $table->text('dice')->nullable();
            $table->text('new_chat_members')->nullable();
            $table->bigInteger('left_chat_member')->nullable();
            $table->string('new_chat_title')->nullable();
            $table->text('new_chat_photo')->nullable();
            $table->tinyInteger('delete_chat_photo')->default(0);
            $table->tinyInteger('group_chat_created')->default(0);
            $table->tinyInteger('supergroup_chat_created')->default(0);
            $table->tinyInteger('channel_chat_created')->default(0);
            $table->bigInteger('migrate_to_chat_id')->nullable();
            $table->bigInteger('migrate_from_chat_id')->nullable();
            $table->text('pinned_message')->nullable();
            $table->text('invoice')->nullable();
            $table->text('successful_payment')->nullable();
            $table->text('connected_website')->nullable();
            $table->text('passport_data')->nullable();
            $table->text('reply_markup')->nullable();
            $table->timestamps();

            $table->primary(['chat_id', 'id']);
            $table->index('user_id');
            $table->index('forward_from');
            $table->index('forward_from_chat');
            $table->index('reply_to_chat');
            $table->index('reply_to_message');
            $table->index('via_bot');
            $table->index('left_chat_member');
            $table->index('migrate_from_chat_id');
            $table->index('migrate_to_chat_id');

            $table->foreign('user_id')->references('id')->on('bot_user')->onDelete('cascade');
            $table->foreign('chat_id')->references('id')->on('bot_chat')->onDelete('cascade');
            $table->foreign('forward_from')->references('id')->on('bot_user')->onDelete('cascade');
            $table->foreign('forward_from_chat')->references('id')->on('bot_chat')->onDelete('cascade');
            $table->foreign('via_bot')->references('id')->on('bot_user')->onDelete('cascade');
            $table->foreign('left_chat_member')->references('id')->on('bot_user')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bot_message');
    }
}
