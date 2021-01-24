<?php

namespace Longman\TelegramBot\Commands\UserCommands;

use Longman\TelegramBot\Commands\Command;
use Longman\TelegramBot\Commands\UserCommand;
use Longman\TelegramBot\Request;

class ContactsCommand extends UserCommand
{
    /**
     * @var string
     */
    protected $name = 'contacts';

    /**
     * @var string
     */
    protected $description = 'Просмотр контактов';

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

        $sendMessage = '*' .setting('site.title') . '*' . PHP_EOL;
        $sendMessage .= 'Телефон: ' . setting('contact.phone') . PHP_EOL;
        $sendMessage .= 'Email: ' . setting('contact.email') . PHP_EOL;
        $sendMessage .= 'Адрес: ' . setting('contact.address') . '(' . setting('contact.landmark') . ')' . PHP_EOL;
        $data = [
            'chat_id'    => $chat_id,
            'text'       => $sendMessage,
            'parse_mode' => 'Markdown',
        ];

        return Request::sendMessage($data);
    }
}
