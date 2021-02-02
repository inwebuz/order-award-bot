<?php

namespace Longman\TelegramBot\Commands\SystemCommands;

use App\Helpers\BotHelper;
use App\Product;
use Illuminate\Support\Facades\Log;
use Longman\TelegramBot\Commands\SystemCommand;
use Longman\TelegramBot\Commands\UserCommands\CatalogueCommand;
use Longman\TelegramBot\Commands\UserCommands\FloorCommand;
use Longman\TelegramBot\Commands\UserCommands\InfoCommand;
use Longman\TelegramBot\Commands\UserCommands\ReviewCommand;
use Longman\TelegramBot\Commands\UserCommands\SendrequestCommand;
use Longman\TelegramBot\DB;
use Longman\TelegramBot\Entities\Keyboard;
use Longman\TelegramBot\Entities\InlineKeyboard;
use Longman\TelegramBot\Entities\Update;
use Longman\TelegramBot\Conversation;
use Longman\TelegramBot\Request;
use Longman\TelegramBot\Commands\UserCommands\ProductCommand;
use Longman\TelegramBot\Commands\UserCommands\CartCommand;
use Longman\TelegramBot\Commands\UserCommands\OrderCommand;
use Longman\TelegramBot\Commands\UserCommands\SettingCommand;
use Longman\TelegramBot\Commands\UserCommands\FreeConsultingCommand;
use Longman\TelegramBot\Commands\UserCommands\GalleryCommand;
use Longman\TelegramBot\Commands\UserCommands\HelpCommand;
use Longman\TelegramBot\Commands\UserCommands\NewsCommand;
use Longman\TelegramBot\Commands\UserCommands\PriceCommand;

/**
 * Generic message command
 *
 * Gets executed when any type of message is sent.
 */
class GenericmessageCommand extends SystemCommand
{
    /**
     * @var string
     */
    protected $name = 'genericmessage';

    /**
     * @var string
     */
    protected $description = 'Handle generic message';

    /**
     * @var string
     */
    protected $version = '1.1.0';

    /**
     * @var bool
     */
    protected $need_mysql = true;

    /**
     * Command execute method if MySQL is required but not available
     *
     * @return \Longman\TelegramBot\Entities\ServerResponse
     * @throws \Longman\TelegramBot\Exception\TelegramException
     */
    public function executeNoDb()
    {
        // Do nothing
        return Request::emptyResponse();
    }

    /**
     * Command execute method
     *
     * @return \Longman\TelegramBot\Entities\ServerResponse
     * @throws \Longman\TelegramBot\Exception\TelegramException
     */
    public function execute()
    {
        $message = $this->getMessage();
        $message_id = $message->getMessageId();
        $chat_id = $message->getChat()->getId();
        $text    = trim($message->getText(true));
        $from       = $message->getFrom();
        $user_id    = $from->getId();

        $pdo = DB::getPdo();
        $lang = BotHelper::getUserLanguage($pdo, $user_id);

        $update = json_decode($this->update->toJson(), true);
        if ($text === BotHelper::t('Button Home', $lang) || $text === '🏠') {
            $update['message']['text'] = '/start';
            return (new StartCommand($this->telegram, new Update($update)))->preExecute();
        } elseif ($text === BotHelper::t('Button Russian') || $text === BotHelper::t('Button Uzbek')) {
            switch ($text) {
                case BotHelper::t('Button Russian'):
                    $newLang = 'ru';
                    break;
                case BotHelper::t('Button Uzbek'):
                    $newLang = 'uz';
                    break;
            }
            $update['message']['text'] = '/start set_language:' . $newLang;
            return (new StartCommand($this->telegram, new Update($update)))->preExecute();
        } elseif ($text === BotHelper::t('Button Service price', $lang)) {
            $update['message']['text'] = '/price';
            return (new PriceCommand($this->telegram, new Update($update)))->preExecute();
        } elseif ($text === BotHelper::t('Button Our products', $lang)) {
            $update['message']['text'] = '/gallery';
            return (new GalleryCommand($this->telegram, new Update($update)))->preExecute();
        } elseif ($text === BotHelper::t('Button Catalog', $lang) || $text === '📂') {
            $update['message']['text'] = '/catalogue';
            return (new CatalogueCommand($this->telegram, new Update($update)))->preExecute();
        } elseif ($text === BotHelper::t('Button Commercial real estate', $lang)) {
            $update['message']['text'] = '/floor floor_type:2';
            return (new FloorCommand($this->telegram, new Update($update)))->preExecute();
        } elseif ($text === BotHelper::t('Button Residential real estate', $lang)) {
            $update['message']['text'] = '/floor floor_type:1';
            return (new FloorCommand($this->telegram, new Update($update)))->preExecute();
        } elseif ($text === BotHelper::t('Button Send request', $lang)) {
            $update['message']['text'] = '/sendrequest';
            return (new SendrequestCommand($this->telegram, new Update($update)))->preExecute();
        } elseif ($text === BotHelper::t('Button Cart', $lang) || $text === '🛒') {
            $update['message']['text'] = '/cart';
            return (new CartCommand($this->telegram, new Update($update)))->preExecute();
        } elseif ($text === BotHelper::t('Button Cart Clear', $lang)) {
            $update['message']['text'] = '/cart clean';
            return (new CartCommand($this->telegram, new Update($update)))->preExecute();
        } elseif ($text === BotHelper::t('Button Help', $lang)) {
            $update['message']['text'] = '/help';
            return (new HelpCommand($this->telegram, new Update($update)))->preExecute();
        } elseif ($text === BotHelper::t('Button Reviews', $lang)) {
            $update['message']['text'] = '/teviews';
            return (new ReviewCommand($this->telegram, new Update($update)))->preExecute();
        } elseif ($text === BotHelper::t('Button Settings', $lang)) {
            $update['message']['text'] = '/setting';
            return (new SettingCommand($this->telegram, new Update($update)))->preExecute();
        } elseif ($text === BotHelper::t('Button Change address', $lang)) {
            $update['message']['text'] = '/setting change:address';
            return (new SettingCommand($this->telegram, new Update($update)))->preExecute();
        } elseif ($text === BotHelper::t('Button Change language', $lang)) {
            $update['message']['text'] = '/setting change:language';
            return (new SettingCommand($this->telegram, new Update($update)))->preExecute();
        } elseif ($text === BotHelper::t('Button Change phone number', $lang)) {
            $update['message']['text'] = '/setting change:phone_number';
            return (new SettingCommand($this->telegram, new Update($update)))->preExecute();
        } else {
            // maybe product
            $product = Product::where('button_text', $text)->first();
            if (!$product && $lang != config('app.locale')) {
                $query = Product::whereHas('translations', function ($q) use ($text) {
                    $q->where('content', 'LIKE', '%"button_text":"' . addslashes(addslashes($text)) . '"%');
                });
                Log::info($query->toSql());
                Log::info($query->getBindings());
                $product = $query->first();
                if ($product) {
                    $product = $product->translateModel($lang);
                }
            }
            if ($product) {
                $update['message']['text'] = '/product product_show:' . $product->id;
                return (new ProductCommand($this->telegram, new Update($update)))->preExecute();
            }
        }

        //If a conversation is busy, execute the conversation command after handling the message
        $conversation = new Conversation(
            $this->getMessage()->getFrom()->getId(),
            $this->getMessage()->getChat()->getId()
        );

        //Fetch conversation command if it exists and execute it
        if ($conversation->exists() && ($command = $conversation->getCommand())) {
            return $this->telegram->executeCommand($command);
        }

        return Request::emptyResponse();
    }
    // public function execute()
    // {
    //     //If a conversation is busy, execute the conversation command after handling the message
    //     $conversation = new Conversation(
    //         $this->getMessage()->getFrom()->getId(),
    //         $this->getMessage()->getChat()->getId()
    //     );

    //     //Fetch conversation command if it exists and execute it
    //     if ($conversation->exists() && ($command = $conversation->getCommand())) {
    //         return $this->telegram->executeCommand($command);
    //     }

    //     return Request::emptyResponse();
    // }
}
