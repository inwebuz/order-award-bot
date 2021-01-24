<?php

namespace Longman\TelegramBot\Commands\SystemCommands;

use App\Helpers\BotHelper;
use App\Product;
use App\Project;
use Illuminate\Support\Facades\Log;
use Longman\TelegramBot\Commands\SystemCommand;
use Longman\TelegramBot\Entities\Keyboard;
use Longman\TelegramBot\Request;
use Longman\TelegramBot\DB;

/**
 * Start command
 *
 * Gets executed when a user first starts using the bot.
 */
class StartCommand extends SystemCommand
{
    /**
     * @var string
     */
    protected $name = 'start';

    /**
     * @var string
     */
    protected $description = 'Start command';

    /**
     * @var string
     */
    protected $usage = '/start';

    /**
     * @var string
     */
    protected $version = '1.1.0';

    /**
     * @var bool
     */
    protected $private_only = true;

    /**
     * Command execute method
     *
     * @return \Longman\TelegramBot\Entities\ServerResponse
     * @throws \Longman\TelegramBot\Exception\TelegramException
     */
    public function execute()
    {
        $message = $this->getMessage();
        $text    = trim($message->getText(true));
        $chat_id = $message->getChat()->getId();
        $user    = $message->getFrom();
        $user_id = $user->getId();

        $pdo = DB::getPdo();

        // check send text
        if (strpos($text, 'set_language') === 0) {
            $getNewLang = explode(':', $text);
            if (!empty($getNewLang[1])) {
                BotHelper::setUserLanguage($pdo, $user_id, $getNewLang[1]);
            }
        }

        // check user info exists or add
        $userInfo = BotHelper::getUserInfo($pdo, $user_id);

        // check user language
        if (empty($userInfo['language'])) {
            $text = BotHelper::t('Choose language', 'common');
            $keyboard = self::getLanguageKeyboard();
        } else {
            $text = BotHelper::t('Welcome', $userInfo['language']);
            $keyboard = self::getKeyboard($userInfo['language']);
        }

        $data = [
            'chat_id' => $chat_id,
            'text'    => $text,
            'reply_markup' => $keyboard,
        ];
        return Request::sendMessage($data);
    }

    public static function getKeyboard($lang = 'ru')
    {
        $buttonRows = [];
        $products = [];
        $getProducts = Product::active()->with('translations')->get();
        foreach ($getProducts as $getProduct) {
            $product = $getProduct->translateModel($lang);
            $products[] = $product->button_text;
        }
        $productRows = array_chunk($products, 2);
        foreach ($productRows as $productRow) {
            $buttonRows[] = $productRow;
        }
        $buttonRows[] = [BotHelper::t('Button Service price', $lang), BotHelper::t('Button Our products', $lang)];
        $buttonRows[] = [BotHelper::t('Button Settings', $lang), /* BotHelper::t('Button Send request', $lang) */];

        $keyboard = new Keyboard(...$buttonRows);

        $keyboard
            ->setResizeKeyboard(true)
            ->setOneTimeKeyboard(false)
            ->setSelective(false);
        return $keyboard;
    }

    public static function getLanguageKeyboard()
    {
        $keyboard = new Keyboard(
            [BotHelper::t('Button Russian'), BotHelper::t('Button Uzbek')]
        );

        $keyboard
            ->setResizeKeyboard(true)
            ->setOneTimeKeyboard(false)
            ->setSelective(false);
        return $keyboard;
    }
}
