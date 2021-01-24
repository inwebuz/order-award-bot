<?php

namespace Longman\TelegramBot\Commands\UserCommands;

use Longman\TelegramBot\Commands\UserCommand;
use Longman\TelegramBot\Entities\Keyboard;
use Longman\TelegramBot\Entities\InlineKeyboard;
use Longman\TelegramBot\Entities\InlineKeyboardButton;
use Longman\TelegramBot\Request;
use Longman\TelegramBot\DB;

class CartCommand extends UserCommand
{
    protected $name = 'cart';
    protected $description = 'Savat';
    protected $usage = '/cart';
    protected $version = '1.0.0';

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

    private $callbackdata;

    public function execute()
    {
        $message = $this->getMessage();
        $message_id = $message->getMessageId();
        $chat_id = $message->getChat()->getId();
        $user = $message->getFrom();
        $user_id = $user->getId();

        $pdo = DB::getPdo();
        $lang = getUserLanguage($pdo, $user_id);

        $data = [
            'chat_id' => $chat_id,
        ];
        $text = trim($message->getText(true));

        // parse data
        $this->callbackdata = parseCallbackData($text);

        $cartItemID = null;
        $cartItemNumber = null;

        // check data
        if (isset($this->callbackdata['cart_item_add'])) {
            // add to cart product

            $product_id = (int)$this->callbackdata['cart_item_add'];
            $cart = getUserCart($pdo, $user_id);
            $cart_id = $cart['id'];

            // check product in cart
            $getCartItem = $pdo->prepare("SELECT * FROM `bot_store_cart_item` WHERE `cart_id` = :cart_id AND `product_id` = :product_id");
            $getCartItem->bindValue(':cart_id', $cart_id);
            $getCartItem->bindValue(':product_id', $product_id);
            $getCartItem->execute();

            if ($getCartItem->rowCount() == 0) {
                // product not in cart yet - add it
                $insertCartItem = $pdo->prepare("INSERT INTO `bot_store_cart_item` (`cart_id`, `product_id`, `quantity`) VALUES (:cart_id, :product_id, 1) ");
                $insertCartItem->bindValue(':cart_id', $cart_id);
                $insertCartItem->bindValue(':product_id', $product_id);
                $insertCartItem->execute();
            } else {
                // product already in cart
                $cartItem = $getCartItem->fetch();
                $cartItemID = $cartItem['id'];
                $quantity = $cartItem['quantity'] + 1;
                $updateCartItem = $pdo->prepare("UPDATE `bot_store_cart_item` SET `quantity` = :quantity WHERE `id` = :id ");
                $updateCartItem->bindValue(':id', $cartItemID);
                $updateCartItem->bindValue(':quantity', $quantity);
                $updateCartItem->execute();
            }

            $data['message_id'] = $message_id;
            $data['reply_markup'] = self::getAddedToCartKeyboard($cart_id, $product_id, $lang);
            return Request::editMessageReplyMarkup($data);

        } elseif (isset($this->callbackdata['cart_item_delete'])) {
            $cartItemID = (int)$this->callbackdata['cart_item_delete'];
            $deleteCartItem = $pdo->prepare("DELETE FROM `bot_store_cart_item` WHERE `id` = :id");
            $deleteCartItem->bindValue(':id', $cartItemID);
            $deleteCartItem->execute();
        } elseif (isset($this->callbackdata['cart_item_decrease'])) {
            $cartItemID = (int)$this->callbackdata['cart_item_decrease'];
            $getCartItem = $pdo->prepare("SELECT * FROM `bot_store_cart_item` WHERE `id` = :id");
            $getCartItem->bindValue(':id', $cartItemID);
            $getCartItem->execute();
            $cartItem = $getCartItem->fetch();
            $quantity = $cartItem['quantity'] - 1;
            if ($quantity > 0) {
                $upateCartItem = $pdo->prepare("UPDATE `bot_store_cart_item` SET `quantity` = :quantity WHERE `id` = :id");
                $upateCartItem->bindValue(':id', $cartItemID);
                $upateCartItem->bindValue(':quantity', $quantity);
                $upateCartItem->execute();
            } else {
                // $deleteCartItem = $pdo->prepare("DELETE FROM `bot_store_cart_item` WHERE `id` = :id");
                // $deleteCartItem->bindValue(':id', $cartItemID);
                // $deleteCartItem->execute();
            }

        } elseif (isset($this->callbackdata['cart_item_increase'])) {
            $cartItemID = (int)$this->callbackdata['cart_item_increase'];
            $getCartItem = $pdo->prepare("SELECT * FROM `bot_store_cart_item` WHERE `id` = :id");
            $getCartItem->bindValue(':id', $cartItemID);
            $getCartItem->execute();
            $cartItem = $getCartItem->fetch();
            $quantity = $cartItem['quantity'] + 1;
            $upateCartItem = $pdo->prepare("UPDATE `bot_store_cart_item` SET `quantity` = :quantity WHERE `id` = :id");
            $upateCartItem->bindValue(':id', $cartItemID);
            $upateCartItem->bindValue(':quantity', $quantity);
            $upateCartItem->execute();
        } elseif (isset($this->callbackdata['cart_item_show'])) {
            $cartItemID = (int)$this->callbackdata['cart_item_show'];
        } elseif (isset($this->callbackdata['cart_item_number_show'])) {
            $cartItemNumber = (int)$this->callbackdata['cart_item_number_show'];
        }

        // show cart
        $result = $this->showCart($cartItemID, $cartItemNumber);
        return $result;
    }

    public static function cartKeyboard($cartItem, $cartItemNumber, $totalItemsNumber, $totalPrice, $lang = 'ru')
    {

        $keyboard = new InlineKeyboard(
            [
                new InlineKeyboardButton(
                    ['text' => '❌', 'callback_data' => 'cart_item_delete:' . $cartItem['id']]
                ),
                new InlineKeyboardButton(
                    ['text' => '🔻', 'callback_data' => 'cart_item_decrease:' . $cartItem['id']]
                ),
                new InlineKeyboardButton(
                    ['text' => $cartItem['quantity'], 'callback_data' => 'empty']
                ),
                new InlineKeyboardButton(
                    ['text' => '🔺', 'callback_data' => 'cart_item_increase:' . $cartItem['id']]
                ),
            ],
            [
                new InlineKeyboardButton(
                    ['text' => '◀', 'callback_data' => 'cart_item_number_show:' . ($cartItemNumber - 1)]
                ),
                new InlineKeyboardButton(
                    ['text' => $cartItemNumber . '/' . $totalItemsNumber, 'callback_data' => 'empty']
                ),
                new InlineKeyboardButton(
                    ['text' => '▶', 'callback_data' => 'cart_item_number_show:' . ($cartItemNumber + 1)]
                )
            ],
            [
                new InlineKeyboardButton(
                    ['text' => '✅ ' . t('Total', $lang) . ': ' . formatPrice($totalPrice, $lang) . '. ' . t('Checkout', $lang), 'callback_data' => 'order_form']
                )
            ]
        );

        return $keyboard;

    }

    public static function getAddedToCartKeyboard($cart_id, $product_id, $lang = 'ru')
    {

        $pdo = DB::getPdo();
        $getCartItem = $pdo->prepare("SELECT * FROM `bot_store_cart_item` WHERE `cart_id` = :cart_id AND `product_id` = :product_id");
        $getCartItem->bindValue(':cart_id', $cart_id);
        $getCartItem->bindValue(':product_id', $product_id);
        $getCartItem->execute();
        $cartItem = $getCartItem->fetch();

        $getProduct = $pdo->prepare("SELECT * FROM `bot_store_product` WHERE `id` = :id");
        $getProduct->bindValue(':id', $product_id);
        $getProduct->execute();
        $product = $getProduct->fetch();

        $keyboard = new InlineKeyboard(
            new InlineKeyboardButton(
                ['text' => $product['title'] . ' ' . formatPrice($product['price'], $lang) . ' (' . $cartItem['quantity'] . ')', 'callback_data' => 'cart_item_add:' . $product_id]
            ),
            new InlineKeyboardButton(
                ['text' => t('Button Cart', $lang), 'callback_data' => 'cart_view:' . $cart_id]
            ),
            new InlineKeyboardButton(
                ['text' => t('Back', $lang), 'callback_data' => 'category_show:' . $product['catalog_id']]
            )
        );

        return $keyboard;

    }

    private function showCart($cartItemID = null, $cartItemNumber = null)
    {
        $message = $this->getMessage();
        $message_id = $message->getMessageId();
        $chat_id = $message->getChat()->getId();
        $user = $message->getFrom();
        $user_id = $user->getId();

        $pdo = DB::getPdo();
        $lang = getUserLanguage($pdo, $user_id);

        $cart = getUserCart($pdo, $user_id);
        $getCartProducts = $pdo->prepare("SELECT sci.*, scp.title, scp.price, scp.url FROM `bot_store_cart_item` sci LEFT JOIN `bot_store_product` scp ON sci.product_id = scp.id WHERE sci.`cart_id` = :cart_id ");
        $getCartProducts->bindValue(':cart_id', $cart['id']);
        $getCartProducts->execute();

        if ($getCartProducts->rowCount() > 0) {
            $cartProducts = $getCartProducts->fetchAll();

            // total
            $totalPrice = 0;
            $totalItemsNumber = count($cartProducts);
            $visibleCartItem = $cartProducts[0];
            $visibleCartItemNumber = 1;

            foreach ($cartProducts as $key => $cartProduct) {
                $totalPrice += $cartProduct['price'] * $cartProduct['quantity'];
                if ($cartProduct['id'] == $cartItemID) {
                    $visibleCartItem = $cartProduct;
                    $visibleCartItemNumber = $key + 1;
                } elseif (($key + 1) == $cartItemNumber) {
                    $visibleCartItem = $cartProduct;
                    $visibleCartItemNumber = $cartItemNumber;
                }
            }

            $data['text'] = $visibleCartItem['title'];
            $visibleCartItemTotal = ($visibleCartItem['price'] * $visibleCartItem['quantity']);

            $text = t('Cart', $lang) . ':' . PHP_EOL;
            $text .= $visibleCartItem['title'] . PHP_EOL;
            $text .= formatPrice($visibleCartItem['price'], $lang) . ' * ' . $visibleCartItem['quantity'] . ' = ' . formatPrice($visibleCartItemTotal, $lang);


            $isCallbackQuery = $this->getCallbackQuery();
            // if callback query - edit cart message (except cart view callback query)
            if ($isCallbackQuery && empty($this->callbackdata['cart_view'])) {
                // callback query - edit cart message
                $data = [
                    'chat_id' => $chat_id,
                    'message_id' => $message_id,
                    'media' => [
                        'type' => 'photo',
                        'caption' => $text,
                        'media' => BASEURL . '/Upload/' . ($visibleCartItem['url'] ? $visibleCartItem['url'] : 'default.jpg'),
                        //'media' => 'http://via.placeholder.com/100x100',
                    ],
                    'reply_markup' => self::cartKeyboard($visibleCartItem, $visibleCartItemNumber, $totalItemsNumber, $totalPrice, $lang),
                ];
                return Request::editMessageMedia($data);
            } else {
                // no callback query - send cart
                $data = [
                    'chat_id' => $chat_id,
                    'caption' => $text,
                    'photo' => BASEURL . '/Upload/' . ($visibleCartItem['url'] ? $visibleCartItem['url'] : 'default.jpg'),
                    //'photo' => 'http://via.placeholder.com/100x100',
                    'reply_markup' => self::cartKeyboard($visibleCartItem, $visibleCartItemNumber, $totalItemsNumber, $totalPrice, $lang)
                ];

                return Request::sendPhoto($data);
            }
        } else {
            $data = [
                'chat_id' => $chat_id,
                'text' => t('No products in cart', $lang)
            ];
            return Request::sendMessage($data);
        }
    }

}
