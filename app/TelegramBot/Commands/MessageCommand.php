<?php

namespace Longman\TelegramBot\Commands\UserCommands;

use App\Helpers\Helper;
use App\TelegramBot\Traits\ManageData;
use Illuminate\Support\Facades\Log;
use Longman\TelegramBot\Commands\Command;
use Longman\TelegramBot\Commands\SystemCommands\StartCommand;
use Longman\TelegramBot\Commands\UserCommand;
use Longman\TelegramBot\Conversation;
use Longman\TelegramBot\Entities\Keyboard;
use Longman\TelegramBot\Entities\KeyboardButton;
use Longman\TelegramBot\Entities\PhotoSize;
use Longman\TelegramBot\Request;

class MessageCommand extends UserCommand
{
    use ManageData;

    /**
     * @var string
     */
    protected $name = 'message';

    /**
     * @var string
     */
    protected $description = 'Отправка сообщения';

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
     * @inheritdoc
     */
    public function execute()
    {
        $message = $this->getMessage();

        $chat = $message->getChat();
        $user = $message->getFrom();
        $text = trim($message->getText(true));
        $chat_id = $chat->getId();
        $user_id = $user->getId();

        // prepare response
        $data = [
            'chat_id' => $chat_id,
        ];

        //Conversation start
        $this->conversation = new Conversation($user_id, $chat_id, $this->getName());

        $notes = &$this->conversation->notes;
        !is_array($notes) && $notes = [];

        if ($text == __('telegram.buttons.back') && isset($notes['state'])) {
            $notes['state']--;
            $text = '';
        }

        //cache data from the tracking session if any
        $state = 0;
        if (isset($notes['state'])) {
            $state = $notes['state'];
        }

        $result = Request::emptyResponse();

        //State machine
        //Entrypoint of the machine state if given by the track
        //Every time a step is achieved the track is updated
        switch ($state) {
            case -1:
                $notes = [];
                $this->conversation->update();
                $this->conversation->stop();
                $data['text'] = 'Отправка сообщения отменена';
                $data['reply_markup'] = StartCommand::keyboard();
                $result = Request::sendMessage($data);
                break;
            case 0:
                if ($text === '') {
                    $notes['state'] = 0;
                    $this->conversation->update();

                    $data['text'] = 'Напишите сообщение:';
                    $data['reply_markup'] = $this->getKeyboard([
                        __('telegram.buttons.back'),
                    ]);

                    $result = Request::sendMessage($data);
                    break;
                }

                $notes['message'] = $text;
                $text = '';

            // no break
            case 1:
                $contact = $message->getContact();
                if ($text === '' && $contact === null) {
                    $notes['state'] = 1;
                    $this->conversation->update();

                    $data['text'] = 'Ваш номер телефона';
                    $keyboardData = [
                        __('telegram.buttons.back'),
                        (new KeyboardButton('Отправить мой номер'))->setRequestContact(true),
                    ];
                    $currentUser = $this->getUser($user_id);
                    if (isset($currentUser->phone)) {
                        $keyboardData[] = $currentUser->phone;
                    }
                    $data['reply_markup'] = $this->getKeyboard($keyboardData);

                    $result = Request::sendMessage($data);
                    break;
                }

                $notes['phone'] = $contact ? $contact->getPhoneNumber() : $text;
                if ($contact) {
                    $this->saveUser($user_id, ['phone' => $contact->getPhoneNumber()]);
                }

                $text = '';

            // no break
            case 2:
                // send message to admin
                $adminMessage = '<strong>' . setting('site.title') . ' - Новое сообщение (Telegram Bot)</strong>' . PHP_EOL;
                $adminMessage .= '<strong>Телефон:</strong> ' . $notes['phone'] . PHP_EOL;
                $adminMessage .= '<strong>Сообщение:</strong> ' . $notes['message'] . PHP_EOL;

                $telegramUser = $this->getTelegramUser($user);
                if (count($telegramUser)) {
                    $adminMessage .= '<strong>Telegram пользователь:</strong> ' . implode(' ', $telegramUser) . PHP_EOL;
                }

                Helper::toTelegram($adminMessage);

                // send message
                $data['text'] = 'Сообщение отправлено, мы свяжемся с вами в ближайшее время';
                $data['reply_markup'] = StartCommand::keyboard();
                $notes = [];
                $this->conversation->update();
                $this->conversation->stop();

                $result = Request::sendMessage($data);
                break;
        }

        return $result;
    }
}
