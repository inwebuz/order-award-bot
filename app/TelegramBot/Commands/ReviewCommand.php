<?php

namespace Longman\TelegramBot\Commands\UserCommands;

use DateTime;
use Longman\TelegramBot\Commands\Command;
use Longman\TelegramBot\Commands\UserCommand;
use Longman\TelegramBot\Entities\InlineKeyboard;
use Longman\TelegramBot\Request;
use Longman\TelegramBot\DB;
use Longman\TelegramBot\Conversation;
use Longman\TelegramBot\Entities\Keyboard;
use Longman\TelegramBot\Entities\KeyboardButton;

/**
 * User "/review" command
 *
 * Command that lists all reviews.
 */
class ReviewCommand extends UserCommand
{
    /**
     * @var string
     */
    protected $name = 'reviews';

    /**
     * @var string
     */
    protected $description = 'Latest reviews';

    /**
     * @var string
     */
    protected $usage = '/reviews';

    /**
     * @var string
     */
    protected $version = '1.0.0';

    /**
     * @var bool
     */
    protected $need_mysql = true;

    private $callbackdata;

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

        $this->callbackdata = parseCallbackData($text);

        $currentPage = 1;
        $reviewsPerPage = 5;
        if (isset($this->callbackdata['reviews_list'])) {
            $currentPage = (int)$this->callbackdata['reviews_list'];
        }

        $pdo = DB::getPdo();
        $lang = getUserLanguage($pdo, $user_id);

        $sendText = t('Reviews', $lang);

        $offset = ($currentPage - 1) * $reviewsPerPage;
        $limit = $reviewsPerPage;
        $countReviews = $pdo->query("SELECT COUNT(*) as cnt FROM `reviews` WHERE `status` = 1")->fetch();
        $reviewsQuantity = !empty($countReviews['cnt']) ? $countReviews['cnt'] : 0;
        $pagesQuantity = ceil($reviewsQuantity / $reviewsPerPage);
        $prevPage = (($currentPage - 1) > 0) ? ($currentPage - 1) : null;
        $nextPage = (($currentPage + 1) <= $pagesQuantity) ? ($currentPage + 1) : null;

        $getReviews = $pdo->prepare("SELECT * FROM `reviews` WHERE `status` = 1 ORDER BY `id` DESC LIMIT :offset, :limit");
        $getReviews->bindValue(':offset', $offset, \PDO::PARAM_INT);
        $getReviews->bindValue(':limit', $limit, \PDO::PARAM_INT);
        $getReviews->execute();

        $reviews = $getReviews->fetchAll();
        foreach ($reviews as $review) {
            $createdAt = DateTime::createFromFormat('Y-m-d H:i:s', $review['created_at']);
            $sendText .= "\n\n" . $review['name'];
            $sendText .= "\n" . $createdAt->format('d-m-Y H:i');
            $sendText .= "\n" . $review['message'];
        }

        $data = [
            'chat_id' => $chat_id,
            'text' => $sendText,
        ];
        $keyboard = self::inlineKeyboard($prevPage, $nextPage, $lang);
        if ($keyboard) {
            $data['reply_markup'] = $keyboard;
        }


        $isCallbackQuery = $this->getCallbackQuery();
        if ($isCallbackQuery) {
            $data['message_id'] = $message->getMessageId();
            $result = Request::editMessageText($data);
        } else {
            $result = Request::sendMessage($data);
        }

        return $result;
    }

    public static function getKeyboard($lang = 'ru')
    {

        $keyboard = new Keyboard(
            [t('Button Back', $lang)]
        );

        $keyboard->setResizeKeyboard(true)->setOneTimeKeyboard(true)->setSelective(false);

        return $keyboard;

    }

    public static function inlineKeyboard($prevPage = 1, $nextPage = 1, $lang = 'ru')
    {
        $buttons = [];
        if ($prevPage) {
            $buttons[] = [
                'text' => '<',
                'callback_data' => 'reviews_list:' . $prevPage,
            ];
        }
        if ($nextPage) {
            $buttons[] = [
                'text' => '>',
                'callback_data' => 'reviews_list:' . $nextPage,
            ];
        }
        $keyboard = $buttons ? new InlineKeyboard(...[$buttons]) : false;

        return $keyboard;
    }
}
