<?php

namespace Longman\TelegramBot\Commands\UserCommands;

use Longman\TelegramBot\Commands\Command;
use Longman\TelegramBot\Commands\UserCommand;
use Longman\TelegramBot\Request;

class OurchannelCommand extends UserCommand
{
    /**
     * @var string
     */
    protected $name = 'ourchannel';

    /**
     * @var string
     */
    protected $description = 'Наш канал';

    /**
     * @var string
     */
    protected $version = '1.0.0';

    /**
     * @inheritdoc
     */
    public function execute()
    {
        $message     = $this->getMessage();
        $chat_id     = $message->getChat()->getId();
        $text        = trim($message->getText(true));

        $sendMessage = '*Подписывайтесь на наш канал*' . PHP_EOL;
        $sendMessage .= '[@ideastore_uz](https://t.me/ideastore_uz)' . PHP_EOL;

        $data = [
            'chat_id'    => $chat_id,
            'text'       => $sendMessage,
            'parse_mode' => 'Markdown',
            'disable_web_page_preview' => true,
        ];

        return Request::sendMessage($data);
    }
}
