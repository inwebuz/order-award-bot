<?php

namespace Longman\TelegramBot\Commands\UserCommands;

use App\Floor;
use App\Helpers\BotHelper;
use App\Project;
use Illuminate\Support\Facades\Log;
use Longman\TelegramBot\Commands\Command;
use Longman\TelegramBot\Commands\SystemCommands\StartCommand;
use Longman\TelegramBot\Commands\UserCommand;
use Longman\TelegramBot\DB;
use Longman\TelegramBot\Entities\InlineKeyboard;
use Longman\TelegramBot\Entities\InputMedia\InputMediaPhoto;
use Longman\TelegramBot\Request;

class ProjectCommand extends UserCommand
{

    /**
     * @var string
     */
    protected $name = 'project';

    /**
     * @var string
     */
    protected $description = 'Информация об объекте';

    /**
     * @var string
     */
    protected $version = '1.0.0';

    private $callbackdata;

    /**
     * @inheritdoc
     */
    public function execute()
    {
        $message = $this->getMessage();
        $chat_id = $message->getChat()->getId();
        $text = trim($message->getText(true));
        $user = $message->getFrom();
        $user_id = $user->getId();

        $pdo = DB::getPdo();
        $lang = BotHelper::getUserLanguage($pdo, $user_id);

        $project = Project::first()->translate($lang);

        // send photo
        if ($project->imgs) {
            $projectImgs = [];
            foreach($project->imgs as $img) {
                $projectImgs[] = new InputMediaPhoto([
                    'media' => config('app.url') . $img,
                ]);
            }
            $resultMedia = Request::sendMediaGroup([
                'chat_id' => $chat_id,
                'media' => $projectImgs,
                //'caption' => $product['title'],
                'disable_notification' => true,
            ]);
        }

        $sendMessage = '';
        // $sendMessage .= '*' . $project->name . '*' . PHP_EOL;
        $sendMessage .= $project->description;

        // get standard keyboard
        $keyboard = StartCommand::getKeyboard($lang);
        $result = Request::sendMessage([
            'chat_id' => $chat_id,
            'text' => $sendMessage,
            'reply_markup' => $keyboard,
            'parse_mode' => 'Markdown',
            'disable_web_page_preview' => true,
        ]);

        return $result;
    }
}
