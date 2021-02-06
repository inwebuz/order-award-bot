<?php

namespace Longman\TelegramBot\Commands\UserCommands;

use App\Helpers\BotHelper;
use App\Helpers\Helper;
use App\Order;
use App\Product;
use Illuminate\Support\Facades\Log;
use Longman\TelegramBot\Commands\UserCommand;
use Longman\TelegramBot\Conversation;
use Longman\TelegramBot\Entities\Keyboard;
use Longman\TelegramBot\Entities\KeyboardButton;
use Longman\TelegramBot\Entities\PhotoSize;
use Longman\TelegramBot\Request;
use Longman\TelegramBot\DB;

use Longman\TelegramBot\Commands\SystemCommands\StartCommand;
use const PHP_EOL;

/**
 * User "/survey" command
 *
 * Command that demonstrated the Conversation funtionality in form of a simple survey.
 */
class OrderCommand extends UserCommand
{
    /**
     * @var string
     */
    protected $name = 'order';

    /**
     * @var string
     */
    protected $description = 'Отправить заявку';

    /**
     * @var string
     */
    protected $usage = '/order';

    /**
     * @var string
     */
    protected $version = '1.0.0';

    /**
     * @var bool
     */
    protected $need_mysql = true;

    /**
     * @var bool
     */
    protected $private_only = true;

    /**
     * Conversation Object
     *
     * @var \Longman\TelegramBot\Conversation
     */
    protected $conversation;

    /**
     * Command execute method
     *
     * @return \Longman\TelegramBot\Entities\ServerResponse
     * @throws \Longman\TelegramBot\Exception\TelegramException
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

        $userInfo = BotHelper::getUserInfo($pdo, $user_id);

        // parse data
        $this->callbackdata = BotHelper::parseCallbackData($text);

        //Preparing Response
        $data = [
            'chat_id' => $chat_id,
        ];

        //Conversation start
        $this->conversation = new Conversation($user_id, $chat_id, $this->getName());

        $notes = &$this->conversation->notes;
        if (!is_array($notes)) {
            $notes = [];
        }

        $result = Request::emptyResponse();

        if (isset($this->callbackdata['order_product'])) {
            $notes['product_id'] = $this->callbackdata['order_product'];
            $text = '';
        }

        if ($text == BotHelper::t('Button Back', $lang)) {
            $notes['state']--;
            $text = '';
        } elseif ($text == BotHelper::t('Button Cancel', $lang)) {
            $notes['state'] = -1;
        }

        // cancel request
        if (!empty($notes['state']) && $notes['state'] == -1) {
            $notes = [];
            $this->conversation->update();
            $this->conversation->stop();
            $data['text'] = BotHelper::t('Order cancelled', $lang);
            $data['reply_markup'] = StartCommand::getKeyboard($lang);
            return Request::sendMessage($data);
        }

        //cache data from the tracking session if any
        $state = 0;
        if (isset($notes['state'])) {
            $state = $notes['state'];
        }

        switch ($state) {
            case 0:
                if ($text === '' || !is_numeric($text)) {
                    $notes['state'] = 0;
                    $this->conversation->update();

                    $data['text'] = BotHelper::t('Choose quantity', $lang);

                    $buttonRows = [];
                    for ($i = 0; $i <= 1; $i++) {
                        $buttonRows[] = array_map(function($val){
                            return (string)$val;
                        }, range($i * 5 + 1, ($i + 1) * 5));
                    }
                    $data['reply_markup'] = (new Keyboard (
                        ...$buttonRows
                    ))
                        ->setOneTimeKeyboard(false)
                        ->setResizeKeyboard(true)
                        ->setSelective(true);

                    $result = Request::sendMessage($data);
                    break;
                }

                $notes['quantity'] = $text;
                $text = '';

            // no break
            case 1:
                $photos = $message->getPhoto();
                if ($text === '' && empty($photos)) {
                    $notes['state'] = 1;
                    $this->conversation->update();

                    $data['text'] = BotHelper::t('Write name of badges or send photo', $lang);

                    $buttons = [BotHelper::t('Button Back', $lang)];
                    $data['reply_markup'] = (new Keyboard (
                        $buttons
                    ))
                        ->setOneTimeKeyboard(false)
                        ->setResizeKeyboard(true)
                        ->setSelective(true);

                    $result = Request::sendMessage($data);
                    break;
                }


                if (!empty($photos)) {
                    $photo = isset($photos[1]) ? $photos[1] : $photos[0];
                    $notes['telegram_file_id'] = $photo->file_id;
                    $notes['products_info'] = $message->getCaption() . ' + PHOTO';
                } else {
                    $notes['products_info'] = $text;
                }

                $text = '';

            // no break
            case 2:
                if ($text === '') {
                    $notes['state'] = 2;
                    $this->conversation->update();

                    $data['text'] = BotHelper::t('Your firstname', $lang);

                    $buttons = [BotHelper::t('Button Back', $lang)];
                    $firstName = $user->getFirstName();
                    if ($firstName) {
                        array_unshift($buttons, $firstName);
                    }
                    $data['reply_markup'] = (new Keyboard (
                        $buttons
                    ))
                        ->setOneTimeKeyboard(false)
                        ->setResizeKeyboard(true)
                        ->setSelective(true);

                    $result = Request::sendMessage($data);
                    break;
                }

                $notes['first_name'] = $text;
                $text = '';

            // no break
            case 3:
                if ($text === '') {
                    $notes['state'] = 3;
                    $this->conversation->update();

                    $data['text'] = BotHelper::t('Your lastname', $lang);

                    $buttons = [BotHelper::t('Button Back', $lang)];
                    $lastName = $user->getLastName();
                    if ($lastName) {
                        array_unshift($buttons, $lastName);
                    }
                    $data['reply_markup'] = (new Keyboard (
                        $buttons
                    ))
                        ->setOneTimeKeyboard(false)
                        ->setResizeKeyboard(true)
                        ->setSelective(true);

                    $result = Request::sendMessage($data);
                    break;
                }

                $notes['last_name'] = $text;
                $text = '';

            // no break
            case 4:
                if ($text === '' && $message->getContact() === null) {
                    $notes['state'] = 4;
                    $this->conversation->update();

                    $data['text'] = BotHelper::t('Contact phone number', $lang) . ' (' . sprintf(BotHelper::t('in format', $lang), '+998001234567') . ')';

                    $buttons = [(new KeyboardButton(BotHelper::t('Button Send my number', $lang)))->setRequestContact(true), BotHelper::t('Button Back', $lang)];
                    if (!empty($userInfo['phone_number'])) {
                        array_unshift($buttons, $userInfo['phone_number']);
                    }

                    $data['reply_markup'] = (new Keyboard(
                        $buttons
                    ))
                        ->setOneTimeKeyboard(false)
                        ->setResizeKeyboard(true)
                        ->setSelective(true);

                    $result = Request::sendMessage($data);
                    break;
                }

                if ($message->getContact() !== null) {
                    $phoneNumber = $message->getContact()->getPhoneNumber();
                } else {
                    $phoneNumber = $text;
                }

                $notes['phone_number'] = BotHelper::reformatPhoneNumber($phoneNumber);

                // save phone to user info if empty
                if (empty($userInfo['phone_number'])) {
                    BotHelper::setUserPhoneNumber($pdo, $user_id, $notes['phone_number']);
                }

                $text = '';


            // no break
            case 5:
                if ($text !== BotHelper::t('Button Confirm', $lang)) {
                    $notes['state'] = 5;
                    $this->conversation->update();

                    $sendMessage = $this->getConfirmText($notes, $lang);

                    $data['reply_markup'] = (new Keyboard(
                        [BotHelper::t('Button Confirm', $lang)],
                        [BotHelper::t('Button Back', $lang), BotHelper::t('Button Cancel', $lang)]
                    ))
                        ->setOneTimeKeyboard(false)
                        ->setResizeKeyboard(true)
                        ->setSelective(true);

                    $data['text'] = $sendMessage;
                    $data['parse_mode'] = 'Markdown';
                    $result = Request::sendMessage($data);
                    break;

                } else {

                    // save to admin
                    $orderData = [
                        'product_id' => $notes['product_id'],
                        'quantity' => $notes['quantity'],
                        'products_info' => $notes['products_info'],
                        'first_name' => $notes['first_name'],
                        'last_name' => $notes['last_name'],
                        'phone_number' => $notes['phone_number'],
                        'info' => '',
                        'status' => Order::STATUS_OPEN,
                    ];

                    // get product
                    $product = $this->getOrderProduct($notes, $lang);
                    if ($product) {
                        $orderData['info'] .= $product->name . "\n";
                        $orderData['total'] = $product->price * $notes['quantity'];
                        $tgUsername = $user->getUsername();
                        if ($tgUsername) {
                            $orderData['info'] .= 'Username: ' . $tgUsername . "\n";
                        }
                        $order = Order::create($orderData);

                        // save image if sent
                        if (!empty($notes['telegram_file_id'])) {
                            $photoFile = Request::getFile(['file_id' => $notes['telegram_file_id']])->getResult();
                            Helper::downloadTelegramFile($photoFile, storage_path('app/public/orders/' . $order->id));
                            $notes['image'] = 'orders/' . $order->id . '/' . $photoFile->getFilePath();
                            $order->image = $notes['image'];
                            $order->telegram_file_id = $notes['telegram_file_id'];
                            $order->save();
                        }

                        // send message to admins group
                        $adminChatID = config('services.telegram.chat_id');
                        // $adminChatID = -483739234;
                        $adminMessage = '*' . BotHelper::t('New order', $lang) . '*' . "\n";
                        $adminMessage .= 'ID: ' . $order->id . "\n";
                        $adminMessage .= $this->getConfirmText($notes, $lang);
                        Request::sendMessage([
                            'chat_id' => $adminChatID,
                            'text' => $adminMessage,
                            'parse_mode' => 'Markdown',
                        ]);

                        // send request accepted message to user
                        $data['text'] = BotHelper::t('Order accepted', $lang) . '!' . "\n";
                        $data['text'] .= BotHelper::t('Order ID', $lang) . ': ' . $order->id . "\n";
                        $data['reply_markup'] = StartCommand::getKeyboard($lang);
                        $data['parse_mode'] = 'Markdown';
                        $result = Request::sendMessage($data);
                    } else {
                        // send request accepted message to user
                        $data['text'] = BotHelper::t('An error has occurred', $lang) . ' ' . BotHelper::t('Please try again', $lang);
                        $data['reply_markup'] = StartCommand::getKeyboard($lang);
                        $data['parse_mode'] = 'Markdown';
                        $result = Request::sendMessage($data);
                    }

                    // clean and stop conversation
                    $notes = [];
                    $this->conversation->update();
                    $this->conversation->stop();

                }

        }

        return $result;
    }

    public function getConfirmText($notes, $lang = 'ru')
    {
        $text = '*' . BotHelper::t('Order details', $lang) . '*' . "\n";
        $text .= BotHelper::t('Name', $lang) . ': ' . $notes['first_name'] . ' ' . $notes['last_name'] . "\n";
        $text .= BotHelper::t('Phone number', $lang) . ': ' . $notes['phone_number'] . "\n";
        if (!empty($notes['product_id'])) {
            $product = $this->getOrderProduct($notes, $lang);
            if ($product) {
                $product = $product->translateModel($lang);
                //$text .= '*' . BotHelper::t('Product', $lang) . '*' . "\n";
                $text .= $product->name . ': ' . $notes['quantity'] . ' ' . ($notes['quantity'] > 1 ? $product->unitsPlural : $product->unitsSingular) . "\n";
            }
        }
        $text .= BotHelper::t('Name of badges', $lang) . ': ' . "\n";
        $text .= $notes['products_info'] . "\n";
        $text .= BotHelper::t('Cost', $lang) . ': ' . BotHelper::formatPrice($product->price * $notes['quantity'], $lang);
        return $text;
    }

    public function getOrderProduct($notes, $lang = 'ru')
    {
        if (!empty($notes['product_id'])) {
            $product = Product::find($notes['product_id']);
        }
        if ($product) {
            return $product;
        }
        return false;
    }
}
