<?php


namespace App\TelegramBot\Traits;


use Illuminate\Support\Facades\DB;
use Longman\TelegramBot\Commands\SystemCommands\StartCommand;
use Longman\TelegramBot\Entities\Keyboard;
use Longman\TelegramBot\Request;

trait ManageData
{
    public function getUser(int $user_id)
    {
        return DB::connection('mysql2')->table('user')->where('id', $user_id)->first();
    }

    public function saveUser(int $user_id, array $data)
    {
        return DB::connection('mysql2')->table('user')->where('id', $user_id)->update($data);
    }

    public function getTelegramUser($user)
    {
        $telegramUser = [];
        if ($user->getUsername()) {
            $telegramUser['username'] = '@' . $user->getUsername();
        }
        if ($user->getFirstName()) {
            $telegramUser['first_name'] = $user->getFirstName();
        }
        if ($user->getLastName()) {
            $telegramUser['last_name'] = $user->getLastName();
        }
        return $telegramUser;
    }

    public function getKeyboard($data = [])
    {
        $data = collect($data)->chunk(3)->toArray();
        $keyboard = new Keyboard(...$data);
        $keyboard
            ->setResizeKeyboard(true)
            ->setOneTimeKeyboard(false)
            ->setSelective(false);
        return $keyboard;
    }

    public function sendErrorMessage($chat_id, $message)
    {
        $keyboard = StartCommand::keyboard();
        return Request::sendMessage([
            'chat_id' => $chat_id,
            'text' => $message,
            'reply_markup' => $keyboard,
        ]);
    }
}
