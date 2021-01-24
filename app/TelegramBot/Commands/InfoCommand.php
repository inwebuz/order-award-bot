<?php

namespace Longman\TelegramBot\Commands\UserCommands;

use App\Helpers\BotHelper;
use App\StaticText;
use Illuminate\Support\Facades\Log;
use Longman\TelegramBot\Commands\Command;
use Longman\TelegramBot\Commands\SystemCommands\StartCommand;
use Longman\TelegramBot\Commands\UserCommand;
use Longman\TelegramBot\DB;
use Longman\TelegramBot\Entities\InlineKeyboard;
use Longman\TelegramBot\Request;

class InfoCommand extends UserCommand
{

    /**
     * @var string
     */
    protected $name = 'info';

    /**
     * @var string
     */
    protected $description = 'Информация';

    /**
     * @var string
     */
    protected $version = '1.0.0';

    private $callbackdata;

    /**
     * @inheritdoc
     */
    public function execute()
    {
        $message = $this->getMessage();
        $chat_id = $message->getChat()->getId();
        $text = trim($message->getText(true));
        $user = $message->getFrom();
        $user_id = $user->getId();

        $pdo = DB::getPdo();
        $lang = BotHelper::getUserLanguage($pdo, $user_id);

        $staticText = StaticText::where('key', 'info_aparto')->first()->translate($lang);
        $sendMessage = $staticText->description;

        // get standard keyboard
        $keyboard = StartCommand::getKeyboard($lang);
        $result = Request::sendMessage([
            'chat_id' => $chat_id,
            'text' => $sendMessage,
            'reply_markup' => $keyboard,
            'parse_mode' => 'Markdown',
            'disable_web_page_preview' => true,
        ]);

        return $result;
    }
}
