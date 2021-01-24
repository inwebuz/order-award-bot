<?php

namespace Longman\TelegramBot\Commands\SystemCommands;

use App\Helpers\BotHelper;
use Illuminate\Support\Facades\Log;
use Longman\TelegramBot\Commands\SystemCommand;
use Longman\TelegramBot\Request;
use Longman\TelegramBot\Entities\Update;
use Longman\TelegramBot\Entities\Keyboard;
use Longman\TelegramBot\Commands\UserCommands\ProductCommand;
use Longman\TelegramBot\Commands\UserCommands\CartCommand;
use Longman\TelegramBot\Commands\UserCommands\OrderCommand;

/**
 * Callback query command
 *
 * This command handles all callback queries sent via inline keyboard buttons.
 *
 * @see InlinekeyboardCommand.php
 */
class CallbackqueryCommand extends SystemCommand
{
    /**
     * @var string
     */
    protected $name = 'callbackquery';

    /**
     * @var string
     */
    protected $description = 'Reply to callback query';

    /**
     * @var string
     */
    protected $version = '1.1.1';

    /**
     * Command execute method
     *
     * @return \Longman\TelegramBot\Entities\ServerResponse
     * @throws \Longman\TelegramBot\Exception\TelegramException
     */
    public function execute()
    {

        $update = $this->getUpdate()->getRawData();
        $callback_query = $this->getCallbackQuery();
        $callback_query_id = $callback_query->getId();
        $callback_data = $callback_query->getData();

        $message = $callback_query->getMessage();
        $message_id = $message->getMessageId();
        $chat_id = $message->getChat()->getId();

        $command = BotHelper::getCallbackCommand($callback_data);
        if ($command) {
            $update['message'] = $update['callback_query']['message'];
            $update['message']['from'] = $update['callback_query']['from'];
            $update['message']['text'] = '/' . $command['command'] . ' ' . $callback_data;
            $commandClass = $command['class'];
            return (new $commandClass($this->telegram, new Update($update)))->preExecute();
        }

        return Request::emptyResponse();

        // $data = [
        // 'callback_query_id' => $callback_query_id,
        // 'text'              => 'Hello World!',
        // 'show_alert'        => $callback_data === 'thumb up',
        // 'cache_time'        => 5,
        // ];

        // return Request::answerCallbackQuery($data);


    }
}
