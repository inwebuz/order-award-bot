<?php

namespace Longman\TelegramBot\Commands\UserCommands;

use Illuminate\Support\Facades\Log;
use Longman\TelegramBot\Commands\Command;
use Longman\TelegramBot\Commands\UserCommand;
use Longman\TelegramBot\DB;
use Longman\TelegramBot\Entities\InlineKeyboard;
use Longman\TelegramBot\Request;

class CatalogueCommand extends UserCommand
{

    /**
     * @var string
     */
    protected $name = 'catalogue';

    /**
     * @var string
     */
    protected $description = 'Просмотр категорий';

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
        $lang = getUserLanguage($pdo, $user_id);

        $sendMessage = t('Catalog', $lang);

        if ($text == '') {
            // get standard keyboard
            $keyboard = $this->keyboardCategories($pdo, [], $lang);
            return Request::sendMessage([
                'chat_id' => $chat_id,
                'text' => $sendMessage,
                'reply_markup' => $keyboard,
            ]);
        } else {
            // parse data
            $this->callbackdata = parseCallbackData($text);

            // get keyboard
            if (isset($this->callbackdata['category_show'])) {
                // show subcategories
                $category_id = (int)$this->callbackdata['category_show'];
                if ($category_id == 0) {
                    $keyboard = $this->keyboardCategories($pdo, [], $lang);
                } else {
                    $getCategory = $pdo->prepare("SELECT sc.*, t.`content` FROM `bot_store_catalog` sc LEFT JOIN `translations` t ON (t.`translatable_id` = sc.`id` AND t.`translatable_type` = :translatable_type AND t.`language` = :language) WHERE sc.`id` = :category_id");
                    $getCategory->bindValue(':language', $lang);
                    $getCategory->bindValue(':translatable_type', 'App\Category');
                    $getCategory->bindValue(':category_id', $category_id);
                    $getCategory->execute();
                    $category = $getCategory->fetch();
                    if (!empty($category['content'])) {
                        $category['content'] =  json_decode($category['content'], true);
                    }
                    $categoryTitle = !empty($category['content']['title']) ? $category['content']['title'] : $category['title'];
                    $sendMessage .= ': ' . $categoryTitle;
                    $keyboard = $this->keyboardCategories($pdo, $category, $lang);
                }
            } elseif (isset($this->callbackdata['category_products'])) {
                // show products
                $category_id = (int)$this->callbackdata['category_products'];
                $getCategory = $pdo->prepare("SELECT sc.*, t.`content` FROM `bot_store_catalog` sc LEFT JOIN `translations` t ON (t.`translatable_id` = sc.`id` AND t.`translatable_type` = :translatable_type AND t.`language` = :language) WHERE sc.`id` = :category_id");
                $getCategory->bindValue(':language', $lang);
                $getCategory->bindValue(':translatable_type', 'App\Category');
                $getCategory->bindValue(':category_id', $category_id);
                $getCategory->execute();
                $category = $getCategory->fetch();
                if (!empty($category['content'])) {
                    $category['content'] =  json_decode($category['content'], true);
                }
                $categoryTitle = !empty($category['content']['title']) ? $category['content']['title'] : $category['title'];
                $sendMessage .= ': ' . $categoryTitle;
                $keyboard = $this->keyboardProducts($pdo, $category, $lang);

            } else {
                // error
                return Request::emptyResponse();
            }

            $isCallbackQuery = $this->getCallbackQuery();

            if ($isCallbackQuery) {
                $result = Request::editMessageText([
                    'chat_id' => $chat_id,
                    'message_id' => $message->getMessageId(),
                    'text' => $sendMessage,
                    'reply_markup' => $keyboard,
                ]);
            } else {
                $result = Request::sendMessage([
                    'chat_id' => $chat_id,
                    'text' => $sendMessage,
                    'reply_markup' => $keyboard,
                ]);
            }

            return $result;
        }
    }

    private function keyboardCategories($pdo, $category = [], $lang = 'ru')
    {
        if ($category === []) {
            $getSubcategories = $pdo->prepare("SELECT sc.*, t.`content` FROM `bot_store_catalog` sc LEFT JOIN `translations` t ON (t.`translatable_id` = sc.`id` AND t.`translatable_type` = :translatable_type AND t.`language` = :language) WHERE sc.`parent_id` IS NULL");
            $getSubcategories->bindValue(':language', $lang);
            $getSubcategories->bindValue(':translatable_type', 'App\Category');
            $getSubcategories->execute();
        } else {
            $getSubcategories = $pdo->prepare("SELECT sc.*, t.`content` FROM `bot_store_catalog` sc LEFT JOIN `translations` t ON (t.`translatable_id` = sc.`id` AND t.`translatable_type` = :translatable_type AND t.`language` = :language) WHERE sc.`parent_id` = :parent_id");
            $getSubcategories->bindValue(':language', $lang);
            $getSubcategories->bindValue(':translatable_type', 'App\Category');
            $getSubcategories->bindValue(':parent_id', $category['id']);
            $getSubcategories->execute();
        }
        $subcategories = $getSubcategories->fetchAll();

        $buttons = [];

        // no children categories - show products keyboard
        if ($category !== [] && !$subcategories) {
            return $this->keyboardProducts($pdo, $category, $lang);
        }

        // show subcategories keyboard
        foreach ($subcategories as $subcategory) {
            if (!empty($subcategory['content'])) {
                $subcategory['content'] =  json_decode($subcategory['content'], true);
            }
            $subcategoryTitle = !empty($subcategory['content']['title']) ? $subcategory['content']['title'] : $subcategory['title'];
            $buttons[] = ['text' => $subcategoryTitle, 'callback_data' => 'category_show:' . $subcategory['id']];
        }

        $buttons = array_chunk($buttons, 2);
        if ($category !== []) {
            $buttons[] = [
                [
                    'text' => t('Show products', $lang),
                    'callback_data' => 'category_products:' . ($category->id ?? 0) . '|page:1',
                ],
            ];
            $buttons[] = [
                [
                    'text' => t('Back', $lang),
                    'callback_data' => 'category_show:' . ($category->parent_id ?? 0),
                ],
            ];
        }

        $inline_keyboard = new InlineKeyboard(...$buttons);

        return $inline_keyboard;
    }

    private function keyboardProducts($pdo, $category, $lang = 'ru')
    {
        $buttons = [];

        $page = isset($this->callbackdata['page']) ? (int)$this->callbackdata['page'] : 1;
        $prevPage = $page - 1;
        $nextPage = $page + 1;

        $limit = 10;
        $offset = ($page - 1) * $limit;
        $getProducts = $pdo->prepare('SELECT sp.*, t.`content` FROM `bot_store_product` sp LEFT JOIN `translations` t ON (t.`translatable_id` = sp.`id` AND t.`translatable_type` = :translatable_type AND t.`language` = :language) WHERE sp.`catalog_id` = :catalog_id ORDER BY sp.`id` DESC LIMIT :offset, :limit');
        $getProducts->bindValue(':translatable_type', 'App\Product');
        $getProducts->bindValue(':language', $lang);
        $getProducts->bindValue(':catalog_id', $category['id']);
        $getProducts->bindValue(':offset', $offset, \PDO::PARAM_INT);
        $getProducts->bindValue(':limit', $limit, \PDO::PARAM_INT);
        $getProducts->execute();
        $products = $getProducts->fetchAll();

        if ($products) {
            foreach ($products as $product) {
                $productTitle = $product['title'];
                if ($product['content']) {
                    $product['content'] = json_decode($product['content'], true);
                    if (!empty($product['content']['title'])) {
                        $productTitle = $product['content']['title'];
                    }
                }
                $buttons[] = ['text' => $productTitle, 'callback_data' => 'product_show:' . $product['id'] . '|return_category:' . $category['id'] . '|return_page:' . $page];
            }
            $buttons = array_chunk($buttons, 2);

            // nav buttons
            $navButtons = [];
            if ($page > 1) {
                $prevPage = $page - 1;
                $navButtons[] = ['text' => '<', 'callback_data' => 'category_products:' . $category['id'] . '|page:' . $prevPage,]; // Пред. стр.
            }
            $navButtons[] = ['text' => '>', 'callback_data' => 'category_products:' . $category['id'] . '|page:' . $nextPage,]; // След. стр.
            $buttons[] = $navButtons;
            $buttons[] = [['text' => t('Back', $lang), 'callback_data' => 'category_show:' . ($category['parent_id'] ?? 0),],]; // Назад
        } else {
            if ($page == 1) {
                $buttons[] = [['text' => t('No more products in the category. Return', $lang), 'callback_data' => 'category_show:' . (int)$category['parent_id']]];
            } else {
                $buttons[] = [['text' => t('No more products. Return last page', $lang), 'callback_data' => 'category_products:' . (int)$category['id'] . '|page:' . $prevPage]];
            }
        }

        $inline_keyboard = new InlineKeyboard(...$buttons);

        return $inline_keyboard;
    }
}
