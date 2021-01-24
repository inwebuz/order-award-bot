<?php

namespace Longman\TelegramBot\Commands\UserCommands;

use App\Helpers\BotHelper;
use App\Helpers\Helper;
use App\Product;
use Longman\TelegramBot\Commands\UserCommand;
use Longman\TelegramBot\DB;
use Longman\TelegramBot\Entities\InlineKeyboard;
use Longman\TelegramBot\Request;

class ProductCommand extends UserCommand
{

    /**
     * @var string
     */
    protected $name = 'product';

    /**
     * @var string
     */
    protected $description = 'Просмотр товара';

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
        if (isset($this->callbackdata['product_show'])) {
            // show subcategories
            $product_id = (int)$this->callbackdata['product_show'];
            $product = Product::findOrFail($product_id);
            $product = $product->translateModel($lang);

            // send photo
            // Request::sendPhoto([
            //     'chat_id' => $chat_id,
            //     'photo' => BASEURL . '/Upload/' . ($product['url'] ? $product['url'] : 'default.jpg'),
            //     //'caption' => $product['title'],
            //     'disable_notification' => true,
            // ]);

            // $returnCategory = isset($this->callbackdata['return_category']) ? $this->callbackdata['return_category'] : null;
            // $returnPage = isset($this->callbackdata['return_page']) ? $this->callbackdata['return_page'] : null;
            $returnCategory = null;
            $returnPage = null;

            // send message
            $sendMessage = '*' . $product->name . '*' . PHP_EOL . PHP_EOL;
            $sendMessage .= ($product->description) ? $product->description . PHP_EOL . PHP_EOL : '';
            $sendMessage .= BotHelper::t('Price', $lang) . ': ' . BotHelper::formatPrice($product['price'], $lang) . ' / ' . $product->unitsSingular . PHP_EOL;

            $keyboard = self::getKeyboard($product, $returnCategory, $returnPage, $lang);
        } else {
            // error
            return Request::emptyResponse();
        }

        return Request::sendMessage([
            'chat_id' => $chat_id,
            'text' => $sendMessage,
            'parse_mode' => 'Markdown',
            'reply_markup' => $keyboard,
        ]);
    }

    public static function getKeyboard($product, $returnCategory = null, $returnPage = null, $lang = 'ru')
    {
        $buttons = [];
        $buttons[] = [
            'text' => BotHelper::t('To Order', $lang),
            'callback_data' => 'order_product:' . $product['id'],
        ];
        if ($returnCategory && $returnPage) {
            $buttons[] = [
                'text' => BotHelper::t('Back', $lang),
                'callback_data' => 'category_products:' . $returnCategory . '|page:' . $returnPage,
            ];
        }

        $inline_keyboard = new InlineKeyboard(...[$buttons]);

        return $inline_keyboard;
    }
}
