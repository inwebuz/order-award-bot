<?php

namespace Longman\TelegramBot\Commands\UserCommands;

use App\Apartment;
use App\Contact;
use App\Helpers\BotHelper;
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
class SendrequestCommand extends UserCommand
{
    /**
     * @var string
     */
    protected $name = 'sendrequest';

    /**
     * @var string
     */
    protected $description = 'Отправить запрос';

    /**
     * @var string
     */
    protected $usage = '/sendrequest';

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

        if (strpos($text, 'getprice_apartment_id:') === 0) {
            $getApartmentID = explode(':', $text);
            $notes['apartment_id'] = !empty($getApartmentID[1]) ? $getApartmentID[1] : 0;
            $text = '';
        } elseif ($text == BotHelper::t('Button Back', $lang)) {
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
            $data['text'] = BotHelper::t('Menu', $lang);
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
                if ($text === '') {
                    $notes['state'] = 0;
                    $this->conversation->update();

                    $data['text'] = BotHelper::t('Your name', $lang);

                    $firstName = $user->getFirstName();
                    $lastName = $user->getLastName();
                    $name = $firstName . ($lastName ? (' ' . $lastName) : '');

                    $data['reply_markup'] = (new Keyboard (
                        [$name, BotHelper::t('Button Back', $lang)]
                    ))
                        ->setOneTimeKeyboard(false)
                        ->setResizeKeyboard(true)
                        ->setSelective(true);

                    $result = Request::sendMessage($data);
                    break;
                }

                $notes['name'] = $text;
                $text = '';

            // no break
            case 1:
                if ($text === '' && $message->getContact() === null) {
                    $notes['state'] = 1;
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
            case 2:
                if ($text === '') {
                    $notes['state'] = 2;
                    $this->conversation->update();

                    $sendMessage = $this->getRequestText($notes, $lang);

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

                } elseif ($text == BotHelper::t('Button Confirm', $lang)) {

                    $managerMessage = '*' . BotHelper::t('New request', $lang) . '*' . "\n";
                    $managerMessage .= $this->getRequestText($notes, $lang);

                    // send message to admins group
                    // -1001470783709
                    Request::sendMessage([
                        'chat_id' => -1001470783709,
                        // 'chat_id' => -483739234, // test group
                        'text' => $managerMessage,
                        'parse_mode' => 'Markdown',
                    ]);

                    // send message to manager
//                    $manager_ids = $this->getConfig('store_manager_id');
//                    $getUserChatId = $pdo->prepare('SELECT `chat_id` FROM ' . TB_USER_CHAT . ' WHERE `user_id` = :user_id');
//                    $getChatId = $pdo->prepare("SELECT `id`, `type` FROM " . TB_CHAT . " WHERE `id` = :id AND `type` = 'private'");
//                    foreach ($manager_ids as $manager_id) {
//                        $getUserChatId->bindValue(':user_id', $manager_id);
//                        $getUserChatId->execute();
//                        if ($getUserChatId->rowCount() > 0) {
//                            $manager_private_chat_id = 0;
//                            $manager_chat_ids = $getUserChatId->fetchAll();
//                            foreach ($manager_chat_ids as $manager_chat_id) {
//                                $getChatId->bindValue(':id', $manager_chat_id['chat_id']);
//                                $getChatId->execute();
//                                if ($getChatId->rowCount() > 0) {
//                                    $manager_private_chat_id = $getChatId->fetch()['id'];
//                                    break;
//                                }
//                            }
//                            if ($manager_private_chat_id == 0) {
//                                continue;
//                            }
//                            Request::sendMessage([
//                                'chat_id' => $manager_private_chat_id,
//                                'text' => $managerMessage,
//                                'parse_mode' => 'Markdown',
//                            ]);
//                        }
//                    }

                    // save to admin
                    $contactData = [
                        'name' => $notes['name'],
                        'phone' => $notes['phone_number'],
                        'subject' => 'getprice_apartment',
                        'info' => '',
                    ];
                    if (!empty($notes['apartment_id'])) {
                        $apartment = Apartment::find($notes['apartment_id']);
                        if ($apartment) {
                            $contactData['info'] .= $apartment->extended_name . "\n";
                        }
                        $tgUsername = $user->getUsername();
                        if ($tgUsername) {
                            $contactData['info'] .= 'Username: ' . $tgUsername . "\n";
                        }
                    }
                    Contact::create($contactData);

                    // send request accepted message to user
                    $data['text'] = BotHelper::t('Request accepted', $lang) . '!';
                    $data['reply_markup'] = StartCommand::getKeyboard($lang);
                    $data['parse_mode'] = 'Markdown';
                    $result = Request::sendMessage($data);

                    // clean and stop conversation
                    $notes = [];
                    $this->conversation->update();
                    $this->conversation->stop();

                }

        }

        return $result;
    }

    public function getRequestText($notes, $lang = 'ru')
    {
        $text = '*' . BotHelper::t('Request details', $lang) . '*' . "\n";
        $text .= BotHelper::t('Name', $lang) . ': ' . $notes['name'] . "\n";
        $text .= BotHelper::t('Phone number', $lang) . ': ' . $notes['phone_number'] . "\n";
        if (!empty($notes['apartment_id'])) {
            $apartment = Apartment::find($notes['apartment_id']);
            if ($apartment) {
                $apartment = $apartment->translate($lang);
                $text .= '*' . BotHelper::t('Premises', $lang) . '*' . "\n";
                $text .= $apartment->extended_name . "\n";
                $text .= 'ID: ' . $apartment->id . "\n";
            }
        }
        return $text;
    }
}
