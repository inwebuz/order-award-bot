<?php

namespace Longman\TelegramBot\Commands\UserCommands;

use Longman\TelegramBot\Commands\UserCommand;
use Longman\TelegramBot\Entities\InlineKeyboard;
use Longman\TelegramBot\Entities\InlineKeyboardButton;
use Longman\TelegramBot\Request;

class FreeConsultingCommand extends UserCommand
{
    protected $name = 'freeconsulting';
    protected $description = 'Бесплатная консультация.';
    protected $usage = '/freeconsulting';
    protected $version = '1.0.0';

    public function execute()
    {
		
		$text = 'Вы можете получить бесплатную консультация обратившись по этим номерам' . "\n";
        $text .= "\n";
        $text .= '+99897 703-28-73' . "\n";
        $text .= '+99899 790-01-05' . "\n";
        $text .= '+99899 797-01-05' . "\n";
        $text .= "\n";
        $text .= '+99871 200-08-01' . "\n";
        $text .= '+99895 142-08-01' . "\n";
        $text .= '+99895 143-08-01' . "\n";
		
        
        $data = [
            'chat_id'      => $this->getMessage()->getChat()->getId(),
            'text'         => $text,
        ];

        return Request::sendMessage($data);
    }
}