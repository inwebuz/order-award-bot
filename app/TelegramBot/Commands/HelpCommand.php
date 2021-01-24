<?php

namespace Longman\TelegramBot\Commands\UserCommands;

use Longman\TelegramBot\Commands\Command;
use Longman\TelegramBot\Commands\UserCommand;
use Longman\TelegramBot\Request;
use Longman\TelegramBot\DB;
use Longman\TelegramBot\Conversation;
use Longman\TelegramBot\Entities\Keyboard;
use Longman\TelegramBot\Entities\KeyboardButton;

/**
 * User "/help" command
 *
 * Command that lists all available commands and displays them in User and Admin sections.
 */
class HelpCommand extends UserCommand
{
    /**
     * @var string
     */
    protected $name = 'help';

    /**
     * @var string
     */
    protected $description = 'Bot commands list';

    /**
     * @var string
     */
    protected $usage = '/help or /help <command>';

    /**
     * @var string
     */
    protected $version = '1.3.0';
    protected $conversation;

    /**
     * @inheritdoc
     */
    public function execute()
    {
        $message = $this->getMessage();
        $chat_id = $message->getChat()->getId();
        $user = $message->getFrom();
        $user_id = $user->getId();
        $text = trim($message->getText(true));

        // Admin commands shouldn't be shown in group chats
        $safe_to_show = $message->getChat()->isPrivateChat();

        $data = [
            'chat_id' => $chat_id,
            'parse_mode' => 'markdown',
        ];

        list($all_commands, $user_commands, $admin_commands) = $this->getUserAdminCommands();

        // If no command parameter is passed, show the list.
        if ($text === '' && $message->getContact() === null) {
            $data['text'] = '*Komandalar*:' . PHP_EOL;
            foreach ($user_commands as $user_command) {
                $data['text'] .= '/' . $user_command->getName() . ' - ' . $user_command->getDescription() . PHP_EOL;
            }

            if ($safe_to_show && count($admin_commands) > 0) {
                $data['text'] .= PHP_EOL . '*Admin Commands List*:' . PHP_EOL;
                foreach ($admin_commands as $admin_command) {
                    $data['text'] .= '/' . $admin_command->getName() . ' - ' . $admin_command->getDescription() . PHP_EOL;
                }
            }

            $data['reply_markup'] = self::getKeyboard();

            return Request::sendMessage($data);
        } elseif ($text === 'call') {

            $data['text'] = 'Telefon: +998990000000';
            $data['reply_markup'] = self::getKeyboard();

            return Request::sendMessage($data);

        } elseif ($text === 'site') {

            $data['text'] = 'To`liq ma\'lumot: https://zipwolf.uz saytida';
            $data['reply_markup'] = self::getKeyboard();

            return Request::sendMessage($data);

        } elseif ($text !== '' || $message->getContact() !== null) {

            $this->conversation = new Conversation($user_id, $chat_id, $this->getName());

            $notes = &$this->conversation->notes;
            !is_array($notes) && $notes = [];

            $result = Request::emptyResponse();
            if ($text === 'Orqaga') {
                $notes['state']--;
                $text = '';
            }

            if (isset($notes['state']) && $notes['state'] == -1) {
                $notes = [];
                $this->conversation->update();
                $this->conversation->stop();
                $data['text'] = 'Выберите какой параметр вы хотите настроить:';
                $data['reply_markup'] = self::getKeyboard();
                return Request::sendMessage($data);
            }
            if (!isset($notes['state'])) {
                $notes['state'] = 0;
            }

            switch ($notes['state']) {

                case 0:

                    if ($text === 'message') {

                        $notes['state'] = 0;
                        $this->conversation->update();

                        $keyboard = new Keyboard(['Orqaga']);
                        $keyboard->setResizeKeyboard(true)->setOneTimeKeyboard(false)->setSelective(true);

                        $data['text'] = 'Xabar qoldiring:';
                        $data['reply_markup'] = $keyboard;

                        $result = Request::sendMessage($data);

                        break;

                    }

                    $notes['message'] = $text;
                    $text = '';

                case 1:
                    if ($text === '' && $message->getContact() === null) {
                        $notes['state'] = 1;
                        $this->conversation->update();

                        $data['reply_markup'] = (new Keyboard(
                            [(new KeyboardButton('Telefon raqamimni yuborish'))->setRequestContact(true), 'Orqaga']
                        ))
                            ->setOneTimeKeyboard(false)
                            ->setResizeKeyboard(true)
                            ->setSelective(true);

                        $data['text'] = 'Aloqa uchun telefon raqami:';

                        $result = Request::sendMessage($data);
                        break;
                    }
                    if ($message->getContact() !== null) {
                        $notes['phone_number'] = $message->getContact()->getPhoneNumber();
                    } else {
                        $notes['phone_number'] = $text;
                    }
                    $text = '';
                case 2:

                    if ($text === '') {

                        $notes['state'] = 2;
                        $this->conversation->update();

                        $pdo = DB::getPdo();

                        $message = $pdo->prepare('INSERT INTO `bot_messages` (`user_id`, `message`) VALUES (:user_id, :message)');

                        $message->bindParam(':user_id', $user_id);
                        $message->bindParam(':message', $notes['message']);
                        $message->execute();


                        $message_for_manager = "Новое сообщение:" . PHP_EOL;
                        $message_for_manager .= "ID: " . $user_id . PHP_EOL;
                        $message_for_manager .= "Ism: " . $user->getFirstName() . ' ' . $user->getLastName() . PHP_EOL;
                        $message_for_manager .= "Telefon: " . $notes['phone_number'] . PHP_EOL;
                        if ($user->getUsername()) {
                            $message_for_manager .= "Username: @" . $user->getUsername() . PHP_EOL;
                        }
                        $message_for_manager .= "Сообщение: " . $notes['message'];

                        $managers_id = $this->getConfig('store_manager_id');

                        for ($i = 0; $i < count($managers_id); $i++) {

                            $result = Request::sendMessage([

                                'chat_id' => $managers_id[$i],
                                'text' => $message_for_manager

                            ]);

                        }

                        $data['text'] = 'Сообщение успешно отправлено!';
                        // $data['reply_markup'] = self::getKeyboard();
                        $data['reply_markup'] = \Longman\TelegramBot\Commands\SystemCommands\StartCommand::getKeyboard();

                        $result = Request::sendMessage($data);

                        $notes = [];
                        $this->conversation->update();
                        $this->conversation->stop();

                        break;

                    }

            }

            return $result;

        }

        $text = str_replace('/', '', $text);
        if (isset($all_commands[$text]) && ($safe_to_show || !$all_commands[$text]->isAdminCommand())) {
            $command = $all_commands[$text];
            $data['text'] = sprintf(
                'Command: %s (v%s)' . PHP_EOL .
                'Description: %s' . PHP_EOL .
                'Usage: %s',
                $command->getName(),
                $command->getVersion(),
                $command->getDescription(),
                $command->getUsage()
            );

            return Request::sendMessage($data);
        }

        $data['text'] = 'No help available: Command /' . $text . ' not found';

        return Request::sendMessage($data);
    }

    public static function getKeyboard()
    {

        $keyboard = new Keyboard(
            ['📞 Позвонить', '✉️Xabar yuborish'],
            // ['📘 Saytda yordam'],
            ['⬅️Orqaga']
        );

        $keyboard->setResizeKeyboard(true)->setOneTimeKeyboard(true)->setSelective(false);

        return $keyboard;

    }

    /**
     * Get all available User and Admin commands to display in the help list.
     *
     * @return Command[][]
     */
    protected function getUserAdminCommands()
    {
        // Only get enabled Admin and User commands that are allowed to be shown.
        /** @var Command[] $commands */
        $commands = array_filter($this->telegram->getCommandsList(), function ($command) {
            /** @var Command $command */
            return !$command->isSystemCommand() && $command->showInHelp() && $command->isEnabled();
        });

        $user_commands = array_filter($commands, function ($command) {
            /** @var Command $command */
            return $command->isUserCommand();
        });

        $admin_commands = array_filter($commands, function ($command) {
            /** @var Command $command */
            return $command->isAdminCommand();
        });

        ksort($commands);
        ksort($user_commands);
        ksort($admin_commands);

        return [$commands, $user_commands, $admin_commands];
    }
}
