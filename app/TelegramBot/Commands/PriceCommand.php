<?php

namespace Longman\TelegramBot\Commands\UserCommands;

use App\Helpers\BotHelper;
use App\Product;
use App\StaticText;
use Illuminate\Support\Facades\Log;
use Longman\TelegramBot\Commands\Command;
use Longman\TelegramBot\Commands\SystemCommands\StartCommand;
use Longman\TelegramBot\Commands\UserCommand;
use Longman\TelegramBot\DB;
use Longman\TelegramBot\Entities\InlineKeyboard;
use Longman\TelegramBot\Request;

class PriceCommand extends UserCommand
{

    /**
     * @var string
     */
    protected $name = 'price';

    /**
     * @var string
     */
    protected $description = 'Стоимость услуг';

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

        $sendMessage = '*' . BotHelper::t('Services cost', $lang) . '*' . PHP_EOL;
        $products = Product::all();
        foreach($products as $product) {
            $sendMessage .= $product->name . ' - ' . BotHelper::formatPrice($product->price, $lang) . ' / ' . $product->unitsSingular . PHP_EOL;
        }

        // get standard keyboard
        $keyboard = StartCommand::getKeyboard($lang);
        $result = Request::sendMessage([
            'chat_id' => $chat_id,
            'text' => $sendMessage,
            'reply_markup' => $keyboard,
            'parse_mode' => 'Markdown',
        ]);

        return $result;
    }
}
