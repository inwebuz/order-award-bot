<?php

namespace Longman\TelegramBot\Commands\UserCommands;

use App\Helpers\BotHelper;
use App\Gallery;
use App\StaticText;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Longman\TelegramBot\Commands\Command;
use Longman\TelegramBot\Commands\SystemCommands\StartCommand;
use Longman\TelegramBot\Commands\UserCommand;
use Longman\TelegramBot\DB;
use Longman\TelegramBot\Entities\InlineKeyboard;
use Longman\TelegramBot\Entities\InputMedia\InputMediaPhoto;
use Longman\TelegramBot\Request;

class GalleryCommand extends UserCommand
{

    /**
     * @var string
     */
    protected $name = 'gallery';

    /**
     * @var string
     */
    protected $description = 'Наша работа';

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

        $sendMessage = '*' . BotHelper::t('Our products', $lang) . '*' . PHP_EOL;
        $galleries = Gallery::latest()->take(15)->get();
        $sendPhotos = [];
        foreach($galleries as $gallery) {
            $sendPhotos[] = new InputMediaPhoto(['media' => Storage::disk('public')->url($gallery->image)]);
        }

        // get standard keyboard
        $keyboard = StartCommand::getKeyboard($lang);
        $result = Request::sendMediaGroup([
            'chat_id' => $chat_id,
            'media' => $sendPhotos,
            //'text' => $sendMessage,
            //'reply_markup' => $keyboard,
            //'parse_mode' => 'Markdown',
        ]);
        Log::info(print_r($result, 1));

        return $result;
    }
}
