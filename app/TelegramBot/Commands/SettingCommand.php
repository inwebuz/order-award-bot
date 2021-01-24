<?php

namespace Longman\TelegramBot\Commands\UserCommands;

use App\Helpers\BotHelper;
use Longman\TelegramBot\Commands\UserCommand;
use Longman\TelegramBot\Conversation;
use Longman\TelegramBot\Entities\Keyboard;
use Longman\TelegramBot\Entities\KeyboardButton;
use Longman\TelegramBot\Entities\PhotoSize;
use Longman\TelegramBot\Request;
use Longman\TelegramBot\DB;

use Longman\TelegramBot\Commands\SystemCommands\StartCommand;

/**
 * User "/survey" command
 *
 * Command that demonstrated the Conversation funtionality in form of a simple survey.
 */
class SettingCommand extends UserCommand
{
    /**
     * @var string
     */
    protected $name = 'setting';

    /**
     * @var string
     */
    protected $description = 'Sozlamalar';

    /**
     * @var string
     */
    protected $usage = '/setting';

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

        $chat = $message->getChat();
        $user = $message->getFrom();
        $text = trim($message->getText(true));
        $chat_id = $chat->getId();
        $user_id = $user->getId();

        $pdo = DB::getPdo();
        $lang = BotHelper::getUserLanguage($pdo, $user_id);

        //Preparing Response
        $data = [
            'chat_id' => $chat_id,
        ];

        $this->conversation = new Conversation($user_id, $chat_id, $this->getName());

        $notes = &$this->conversation->notes;
        if (!is_array($notes)) {
            $notes = [];
        }
        if (empty($notes['state'])) {
            $notes['state'] = 0;
        }

        $result = Request::emptyResponse();

        if ($text === BotHelper::t('Button Back', $lang)) {
            $text = '';
            $notes['state']--;
        }

        // return if
        if ($notes['state'] == -1) {
            $notes = [];
            $this->conversation->update();
            $this->conversation->stop();
            $data['text'] = BotHelper::t('Menu', $lang);
            $data['reply_markup'] = StartCommand::getKeyboard($lang);
            return Request::sendMessage($data);
        }

        switch ($notes['state']) {
            case 0:
                $notes['state'] = 0;
                $this->conversation->update();
                if ($text == '' || strpos($text, 'change:') !== 0) {
                    $data['text'] = BotHelper::t('Choose setting', $lang);
                    $data['reply_markup'] = self::getKeyboard($lang);
                    $result = Request::sendMessage($data);
                    break;
                }
                $notes['setting_type'] = $text;
                $text = '';

            // no break
            case 1:
                $notes['state'] = 1;
                $this->conversation->update();
                $userInfo = BotHelper::getUserInfo($pdo, $user_id);
                if ($notes['setting_type'] === 'change:address') {
                    if ($text == '') {
                        $data['text'] = BotHelper::t('Your address', $lang) . ': ' . $userInfo['address'] . PHP_EOL . BotHelper::t('Enter new address', $lang);
                        $data['reply_markup'] = self::miniKeyboard($lang);
                        $result = Request::sendMessage($data);
                        break;
                    } else {
                        // update setting
                        BotHelper::setUserAddress($pdo, $user_id, $text);

                        // update conversation state
                        $notes['state'] = 0;
                        $this->conversation->update();

                        // send message
                        $data['text'] = BotHelper::t('Address saved', $lang);
                        $data['reply_markup'] = self::getKeyboard($lang);
                        $result = Request::sendMessage($data);
                    }
                } elseif ($notes['setting_type'] === 'change:language') {
                    // stop conversation
                    $notes = [];
                    $this->conversation->update();
                    $this->conversation->stop();

                    // send keyboard
                    $data['text'] = BotHelper::t('Choose language', 'common');
                    $data['reply_markup'] = StartCommand::getLanguageKeyboard();
                    $result = Request::sendMessage($data);
                } elseif ($notes['setting_type'] === 'change:phone_number') {
                    if ($text == '' && $message->getContact() === null) {
                        $data['text'] = BotHelper::t('Your phone number', $lang) . ': ' . $userInfo['phone_number'] . PHP_EOL . BotHelper::t('Enter new phone number', $lang);
                        $data['reply_markup'] = self::phoneNumberKeyboard($lang);
                        $result = Request::sendMessage($data);
                        break;
                    } else {

                        if ($message->getContact() !== null) {
                            $phoneNumber = $message->getContact()->getPhoneNumber();
                        } else {
                            $phoneNumber = $text;
                        }

                        // update setting
                        BotHelper::setUserPhoneNumber($pdo, $user_id, $phoneNumber);

                        // update conversation state
                        $notes['state'] = 0;
                        $this->conversation->update();

                        // send message
                        $data['text'] = BotHelper::t('Phone number saved', $lang)  ;
                        $data['reply_markup'] = self::getKeyboard($lang);
                        $result = Request::sendMessage($data);
                    }
                }
        }
        return $result;

    }

    public static function locationKeyboard($lang = 'ru')
    {

        $keyboard = new Keyboard(
            [
                ['text' => 'Lokatsiya yuborish', 'request_location' => true],
            ],
            [BotHelper::t('Button Back', $lang)]
        );

        $keyboard
            ->setResizeKeyboard(true)
            ->setOneTimeKeyboard(false)
            ->setSelective(true);

        return $keyboard;

    }

    public static function notificationKeyboard($lang = 'ru')
    {

        $keyboard = (new Keyboard(
            ['🔕 O`chirish', '🔔 Yoqish'],
            [BotHelper::t('Button Back', $lang)]
        ))->setResizeKeyboard(true)
            ->setOneTimeKeyboard(false)
            ->setSelective(true);

        return $keyboard;

    }

    public static function phoneNumberKeyboard($lang = 'ru')
    {

        $keyboard = new Keyboard(
            [
                ['text' => BotHelper::t('Button Send my number', $lang), 'request_contact' => true],
            ],
            [BotHelper::t('Button Back', $lang)]
        );

        $keyboard
            ->setResizeKeyboard(true)
            ->setOneTimeKeyboard(false)
            ->setSelective(true);

        return $keyboard;

    }

    public static function miniKeyboard($lang = 'ru')
    {
        $keyboard = (new Keyboard (
            [BotHelper::t('Button Back', $lang)]
        ))
            ->setOneTimeKeyboard(false)
            ->setResizeKeyboard(true)
            ->setSelective(true);
        return $keyboard;
    }

    public static function getKeyboard($lang = 'ru')
    {

        $keyboard = new Keyboard(
            [BotHelper::t('Button Change language', $lang), BotHelper::t('Button Change phone number', $lang)],
            //[BotHelper::t('Button Change address', $lang), BotHelper::t('Button Back', $lang),]
            [BotHelper::t('Button Back', $lang),]
        );

        $keyboard->setResizeKeyboard(true)->setOneTimeKeyboard(false)->setSelective(false);

        return $keyboard;

    }

}
