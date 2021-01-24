<?php

namespace Longman\TelegramBot\Commands\UserCommands;

use App\Apartment;
use App\Floor;
use App\Helpers\BotHelper;
use Illuminate\Support\Facades\Log;
use Longman\TelegramBot\Commands\Command;
use Longman\TelegramBot\Commands\UserCommand;
use Longman\TelegramBot\DB;
use Longman\TelegramBot\Entities\InlineKeyboard;
use Longman\TelegramBot\Request;
use const PHP_EOL;

class FloorCommand extends UserCommand
{

    /**
     * @var string
     */
    protected $name = 'floor';

    /**
     * @var string
     */
    protected $description = 'Просмотр жилой недвижимости';

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

        // parse data
        $this->callbackdata = BotHelper::parseCallbackData($text);

        if ($text == '') {
        	$sendMessage = BotHelper::t('Real estate', $lang);
            // get standard keyboard
            $keyboard = $this->keyboardFloors(0, $lang);
            $result = Request::sendMessage([
                'chat_id' => $chat_id,
                'text' => $sendMessage,
                'reply_markup' => $keyboard,
            ]);
        } elseif (isset($this->callbackdata['floor_type'])) {
            // get standard keyboard
            $floorType = $this->callbackdata['floor_type'];
            $sendMessage = ($floorType == 1) ? BotHelper::t('Residential real estate', $lang) : BotHelper::t('Commercial real estate', $lang);;
            $keyboard = $this->keyboardFloors($floorType, $lang);
            $result = Request::sendMessage([
                'chat_id' => $chat_id,
                'text' => $sendMessage,
                'reply_markup' => $keyboard,
            ]);
        } elseif (isset($this->callbackdata['floor_show'])) {
            // show subcategories
            $floor_id = (int)$this->callbackdata['floor_show'];
            $floor = Floor::find($floor_id);
            if ($floor) {
                // get standard keyboard

                // send photo
                $floorImg = (!empty($floor->imgs[0])) ? $floor->imgs[0] : '';
                if ($floorImg) {
                    $resultPhoto = Request::sendPhoto([
                        'chat_id' => $chat_id,
                        'photo' => config('app.url') . $floorImg . '?v=2',
                        //'caption' => $product['title'],
                        'disable_notification' => true,
                    ]);
                }

                $floor = $floor->translate($lang);

                // send message with keyboard
                $sendMessage = $floor->name . PHP_EOL;
                $sendMessage .= ($floor->type == Floor::TYPE_RESIDENTIAL) ? BotHelper::t('Choose apartment', $lang) : BotHelper::t('Choose premises', $lang);
                $keyboard = $this->keyboardApartments($floor->id, $lang);
                $result = Request::sendMessage([
                    'chat_id' => $chat_id,
                    'text' => $sendMessage,
                    'reply_markup' => $keyboard,
                ]);
            } else {
                $result = Request::emptyResponse();
            }
        } else {
            $result = Request::emptyResponse();
        }
        return $result;
    }

    private function keyboardFloors($floorType = 0, $lang = 'ru')
    {
        $query = Floor::orderBy('order');
        if ($floorType) {
            $query->where('type', $floorType);
        }
        $floors = $query->get();
        $buttons = [];

        // show subcategories keyboard
        if ($floors) {
            $floors = $floors->translate($lang);
            foreach ($floors as $floor) {
                $buttons[] = ['text' => $floor->name, 'callback_data' => 'floor_show:' . $floor->id];
            }
        } else {
            $buttons[] = ['text' => BotHelper::t('No information', $lang), 'callback_data' => ''];
        }

        $buttons = array_chunk($buttons, 1);

        $inline_keyboard = new InlineKeyboard(...$buttons);

        return $inline_keyboard;
    }

    private function keyboardApartments($floor_id, $lang = 'ru')
    {
        $apartments = Apartment::where('floor_id', $floor_id)->orderBy('number')->get();
        $buttons = [];

        // show subcategories keyboard
        if ($apartments) {
            $apartments = $apartments->translate($lang);
            foreach ($apartments as $apartment) {
                $name = '';
                if (!$apartment->getModel()->isActive()) {
                    $name .= '(' . __('main.sold') . ') ';
                }
                $name .= $apartment->extended_name;
                $buttons[] = ['text' => $name, 'callback_data' => 'apartment_show:' . $apartment->id];
            }
        } else {
            $buttons[] = ['text' => BotHelper::t('No information', $lang), 'callback_data' => ''];
        }

        $buttons = array_chunk($buttons, 2);

        $inline_keyboard = new InlineKeyboard(...$buttons);

        return $inline_keyboard;
    }
}
