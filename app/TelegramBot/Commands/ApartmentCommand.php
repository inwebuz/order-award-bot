<?php

namespace Longman\TelegramBot\Commands\UserCommands;

use App\Apartment;
use App\Helpers\BotHelper;
use Illuminate\Support\Facades\Log;
use Longman\TelegramBot\Commands\UserCommand;
use Longman\TelegramBot\DB;
use Longman\TelegramBot\Entities\InlineKeyboard;
use Longman\TelegramBot\Request;
use const PHP_EOL;

class ApartmentCommand extends UserCommand
{

    /**
     * @var string
     */
    protected $name = 'apartment';

    /**
     * @var string
     */
    protected $description = 'Просмотр помещения';

    /**
     * @var string
     */
    protected $version = '1.0.0';

    /**
     * @var array
     */
    private $callbackdata;

    /**
     * @inheritdoc
     */
    public function execute()
    {
        $message = $this->getMessage();
        $user = $message->getFrom();
        $chat = $message->getChat();
        $chat_id = $chat->getId();
        $user_id = $user->getId();
        $text = trim($message->getText(true));

        $pdo = DB::getPdo();
        $lang = BotHelper::getUserLanguage($pdo, $user_id);

        // parse data
        $this->callbackdata = BotHelper::parseCallbackData($text);

        // get keyboard
        if (isset($this->callbackdata['apartment_show'])) {
            $apartment_id = (int)$this->callbackdata['apartment_show'];
            $apartment = Apartment::find($apartment_id)->translate();

            // send photo
            $apartmentImg = (!empty($apartment->imgs[0])) ? $apartment->imgs[0] : '';
            if ($apartmentImg) {
                // $apartmentImgPath = storage_path('app/public') . $apartmentImg;
                $resultPhoto = Request::sendPhoto([
                    'chat_id' => $chat_id,
                    // 'photo' => Request::encodeFile($apartmentImgPath),
                    'photo' => config('app.url') . $apartmentImg . '?v=11',
                    //'caption' => $product['title'],
                    'disable_notification' => true,
                ]);
            }

            // $apartment = $apartment->translate($lang);

            // send message
            $sendMessage = '';
            if (!$apartment->getModel()->isActive()) {
                $sendMessage .= __('main.sold') . PHP_EOL;
            }
            $sendMessage .= '*' . $apartment->extended_name . '*' . PHP_EOL;
            // $sendMessage .= $apartment->description;
            $keyboard = self::getKeyboard($apartment, $lang);
        } else {
            // error
            return Request::emptyResponse();
        }

        return Request::sendMessage([
            'chat_id' => $chat_id,
            'text' => $sendMessage,
            'reply_markup' => $keyboard,
            'parse_mode' => 'Markdown',
        ]);
    }

    public static function getKeyboard($apartment, $lang = 'ru')
    {
        $buttons = [];
        $buttons[] = [
            'text' => BotHelper::t('Get price', $lang),
            'callback_data' => 'getprice_apartment_id:' . $apartment->id,
        ];

        $inline_keyboard = new InlineKeyboard(...[$buttons]);

        return $inline_keyboard;
    }
}
