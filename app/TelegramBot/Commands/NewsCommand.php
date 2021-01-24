<?php

namespace Longman\TelegramBot\Commands\UserCommands;

use Longman\TelegramBot\Commands\UserCommand;
use Longman\TelegramBot\Entities\Keyboard;
use Longman\TelegramBot\Request;
use Longman\TelegramBot\DB;

use Longman\TelegramBot\Commands\SystemCommands\StartCommand;

class NewsCommand extends UserCommand
{
    protected $name = 'news';
    protected $description = 'Yangiliklar';
    protected $usage = '/news';
    protected $version = '1.0.0';
    protected $need_mysql = true;
    protected $private_only = true;

    public function execute(){
        
        $message = $this->getMessage();
        $chat = $message->getChat();
        $chat_id = $chat->getId();
        $user = $message->getFrom();
        $user_id = $user->getId();
        
        $keyboard = (new Keyboard(
            ['⬅️Orqaga']
        
        ))->setResizeKeyboard(true)->setOneTimeKeyboard(true)->setSelective(false);
        
        $pdo = DB::getPdo();
        $news = $pdo->prepare("SELECT * FROM `bot_news` ORDER BY id DESC LIMIT 1");
        $news->execute();
        
        if($news->rowCount() > 0){
            foreach($news as $new){
                $latest = $new['news'];
            }
            
            $data = [
            
                'chat_id' => $chat_id,
                'text' => $latest,
                'reply_markup' => $keyboard
            
            ];
            
            return Request::sendMessage($data); 
            
        }else{
            
            $data = [
            
                'chat_id' => $chat_id,
                'text' => 'Yangiliklar yo`q!',
                'reply_markup' => $keyboard
            
            ];
            
            return Request::sendMessage($data); 
            
        }
        
        
    }

    
}
