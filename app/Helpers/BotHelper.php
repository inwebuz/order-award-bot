<?php


namespace App\Helpers;


use Longman\TelegramBot\Commands\UserCommands\ApartmentCommand;
use Longman\TelegramBot\Commands\UserCommands\CartCommand;
use Longman\TelegramBot\Commands\UserCommands\CatalogueCommand;
use Longman\TelegramBot\Commands\UserCommands\FloorCommand;
use Longman\TelegramBot\Commands\UserCommands\OrderCommand;
use Longman\TelegramBot\Commands\UserCommands\ProductCommand;
use Longman\TelegramBot\Commands\UserCommands\ReviewCommand;
use Longman\TelegramBot\Commands\UserCommands\SendrequestCommand;

class BotHelper
{
    public static function t($key, $lang = 'common')
    {
        return self::translations()[$lang][$key] ?? $key;
    }

    public static function parseCallbackData($callback_data)
    {
        $data = [];
        $callback_data = explode('|', $callback_data);
        foreach ($callback_data as $key => $value) {
            $row = explode(':', $value);
            $data[$row[0]] = isset($row[1]) ? $row[1] : '';
        }
        return $data;
    }

    public static function getCallbackCommand($data)
    {
        $command = [];
        $data = self::parseCallbackData($data);
        foreach (self::keyCommands() as $key => $value) {
            if (isset($data[$key])) {
                $command = $value;
                break;
            }
        }
        return $command;
    }

    public static function getUserInfo($pdo, $user_id)
    {
        $getUserInfo = $pdo->prepare("SELECT * FROM `bot_user_info` WHERE `user_id` = :user_id");
        $getUserInfo->bindParam(':user_id', $user_id);
        $getUserInfo->execute();

        if ($getUserInfo->rowCount() == 0) {
            $stmt = $pdo->prepare("INSERT INTO `bot_user_info` (`user_id`) VALUES (:user_id)");
            $stmt->bindParam(':user_id', $user_id);
            $stmt->execute();
            $getUserInfo = $pdo->prepare("SELECT * FROM `bot_user_info` WHERE `user_id` = :user_id");
            $getUserInfo->bindParam(':user_id', $user_id);
            $getUserInfo->execute();
        }
        return $getUserInfo->fetch();
    }

    public static function getUserLanguage($pdo, $user_id)
    {
        $userInfo = self::getUserInfo($pdo, $user_id);
        return $userInfo['language'] ?? 'ru';
    }

    public static function getUserCart($pdo, $user_id)
    {
        $getUserCart = $pdo->prepare("SELECT * FROM `bot_store_cart` WHERE `user_id` = :user_id");
        $getUserCart->bindParam(':user_id', $user_id);
        $getUserCart->execute();

        if ($getUserCart->rowCount() == 0) {
            $stmt = $pdo->prepare("INSERT INTO `bot_store_cart` (`user_id`) VALUES (:user_id)");
            $stmt->bindParam(':user_id', $user_id);
            $stmt->execute();
            $getUserCart = $pdo->prepare("SELECT * FROM `bot_store_cart` WHERE `user_id` = :user_id");
            $getUserCart->bindParam(':user_id', $user_id);
            $getUserCart->execute();
        }
        return $getUserCart->fetch();
    }

    public static function setUserLanguage($pdo, $user_id, $lang)
    {
        self::getUserInfo($pdo, $user_id);
        $stmt = $pdo->prepare("UPDATE `bot_user_info` SET `language` = :language WHERE `user_id` = :user_id");
        $stmt->bindParam(':language', $lang);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
    }

    public static function reformatPhoneNumber($phoneNumber)
    {
        $phoneNumber = preg_replace('#[^\d]#', '', $phoneNumber);
        if (mb_strlen($phoneNumber) == 9) {
            $phoneNumber = '+998' . $phoneNumber;
        } elseif (mb_strlen($phoneNumber) == 12) {
            $phoneNumber = '+' . $phoneNumber;
        } else {
            //return '';
        }
        return $phoneNumber;
    }

    public static function setUserPhoneNumber($pdo, $user_id, $phoneNumber)
    {
        self::getUserInfo($pdo, $user_id);
        $stmt = $pdo->prepare("UPDATE `bot_user_info` SET `phone_number` = :phone_number WHERE `user_id` = :user_id");
        $stmt->bindParam(':phone_number', $phoneNumber);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
    }

    public static function setUserAddress($pdo, $user_id, $address)
    {
        self::getUserInfo($pdo, $user_id);
        $stmt = $pdo->prepare("UPDATE `bot_user_info` SET `address` = :address WHERE `user_id` = :user_id");
        $stmt->bindParam(':address', $address);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
    }

    public static function setUserLatitude($pdo, $user_id, $latitude)
    {
        self::getUserInfo($pdo, $user_id);
        $stmt = $pdo->prepare("UPDATE `bot_user_info` SET `latitude` = :latitude WHERE `user_id` = :user_id");
        $stmt->bindParam(':latitude', $latitude);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
    }

    public static function setUserLongitude($pdo, $user_id, $longitude)
    {
        self::getUserInfo($pdo, $user_id);
        $stmt = $pdo->prepare("UPDATE `bot_user_info` SET `longitude` = :longitude WHERE `user_id` = :user_id");
        $stmt->bindParam(':longitude', $longitude);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
    }

    public static function addToCart($pdo, $user_id, $product_id)
    {

        $products = [];
        //get user cart id
        $getCartId = $pdo->prepare('SELECT * FROM `bot_store_cart` WHERE user_id = :user_id');
        $getCartId->bindParam(':user_id', $user_id);
        $getCartId->execute();

        if ($getCartId->rowCount() > 0) {
            $cart = $getCartId->fetch();
            $cart_id = $cart['id'];
        } else {
            $insertCart = $pdo->prepare('INSERT INTO `bot_store_cart` (user_id) VALUES (:user_id)');
            $insertCart->bindParam(':user_id', $user_id);
            $insertCart->execute();
            $cart_id = $pdo->lastInsertId();
        }

        //check item exists in cart
        $getCartItem = $pdo->prepare('SELECT * FROM `bot_store_cart_item` WHERE cart_id = :cart_id AND product_id = :product_id');
        $getCartItem->bindParam(':cart_id', $cart_id);
        $getCartItem->bindParam(':product_id', $product_id);
        $getCartItem->execute();

        //updating cart item
        if ($getCartItem->rowCount() > 0) {
            $cartItem = $getCartItem->fetch();
            $updateCartItem = $pdo->prepare('UPDATE `bot_store_cart_item` SET quantity = :quantity WHERE id = :id');
            $updateCartItem->bindParam(':id', $cartItem['id']);
            $quantity = (int)$cartItem['quantity'] + 1;
            $updateCartItem->bindParam(':quantity', $quantity);
            $updateCartItem->execute();
        } //inserting new item to cart
        else {

            $insertCartItem = $pdo->prepare('INSERT INTO `bot_store_cart_item` (cart_id, product_id, quantity) VALUES (:cart_id, :product_id, 1)');
            $insertCartItem->bindParam(':cart_id', $cart_id);
            $insertCartItem->bindParam(':product_id', $product_id);
            $insertCartItem->execute();
        }
    }

    public static function formatPrice($price, $lang = 'ru')
    {
        return number_format($price, 0, '.', ' ') . ' ' . BotHelper::t('Currency sum', $lang);
    }

    public static function ppp($text)
    {
        file_put_contents('ppp.txt', print_r($text, true));
    }

    public static function ppp2($text)
    {
        file_put_contents('ppp2.txt', print_r($text, true));
    }

    public static function keyCommands()
    {
        return [
            'getprice_apartment_id' => [
                'command' => 'sendrequest',
                'class' => SendrequestCommand::class
            ],
            'apartment_show' => [
                'command' => 'apartment',
                'class' => ApartmentCommand::class
            ],
            'floor_show' => [
                'command' => 'floor',
                'class' => FloorCommand::class
            ],
            'category_show' => [
                'command' => 'catalogue',
                'class' => CatalogueCommand::class
            ],
            'category_products' => [
                'command' => 'catalogue',
                'class' => CatalogueCommand::class
            ],
            'catalogue' => [
                'command' => 'catalogue',
                'class' => CatalogueCommand::class
            ],
            'product_show' => [
                'command' => 'product',
                'class' => ProductCommand::class
            ],
            'cart_view' => [
                'command' => 'cart',
                'class' => CartCommand::class
            ],
            'cart_item_add' => [
                'command' => 'cart',
                'class' => CartCommand::class
            ],
            'cart_item_delete' => [
                'command' => 'cart',
                'class' => CartCommand::class
            ],
            'cart_item_decrease' => [
                'command' => 'cart',
                'class' => CartCommand::class
            ],
            'cart_item_increase' => [
                'command' => 'cart',
                'class' => CartCommand::class
            ],
            'cart_item_show' => [
                'command' => 'cart',
                'class' => CartCommand::class
            ],
            'cart_item_number_show' => [
                'command' => 'cart',
                'class' => CartCommand::class
            ],
            'order' => [
                'command' => 'order',
                'class' => OrderCommand::class
            ],
            'order_form' => [
                'command' => 'order',
                'class' => OrderCommand::class
            ],
            'order_product' => [
                'command' => 'order',
                'class' => OrderCommand::class
            ],
            'reviews_list' => [
                'command' => 'reviews',
                'class' => ReviewCommand::class
            ],
        ];
    }

    public static function translations()
    {
        return [
            'common' => [
                'Button Russian' => '🇷🇺 Русский',
                'Button Uzbek' => '🇺🇿 Oʻzbekcha',
                'Choose language' => 'Выберите язык / Tilni tanlang',
                '' => '',
            ],
            'ru' => [
                'Add to cart' => 'Добавить в корзину',
                'Address' => 'Адрес',
                'Address saved' => 'Адрес сохранен',
                'Apartment' => 'Квартира',
                'Back' => 'Назад',
                'Button Back' => '⬅️ Назад',
                'Button Cancel' => '✖️ Отменить',
                'Button Cart' => '🛒 Корзина',
                'Button Cart Clear' => '❌ Очистить корзину',
                'Button Catalog' => '📂 Каталог',
                'Button Change address' => '🏡 Изменить адрес',
                'Button Change language' => '🏳️ Изменить язык',
                'Button Change phone number' => '📞 Изменить номер телефона',
                'Button Commercial real estate' => '🏬 Коммерческая недвижимость',
                'Button Computers' => '🖥 Компьютеры',
                'Button Confirm' => '✔️ Подтвердить',
                'Button Free delivery' => '🚗 Бесплатная доставка',
                'Button Help' => '❓ Помощь',
                'Button Home' => '🏠 Главная старница',
                'Button Info Aparto' => '📋 Info Aparto',
                'Button Laptops' => '💻 Ноутбуки',
                'Button Monoblocks' => '🖥 Моноблоки',
                'Button Orders' => '🥡 Заказы',
                'Button Our products' => '🏅 Наша продукция',
                'Button Residential real estate' => '🏢 Жилая недвижимость',
                'Button Reviews' => '💬 Отзывы',
                'Button Send message' => '✉️Отправить сообщение',
                'Button Send my number' => 'Отправить мой номер',
                'Button Send request' => '✏️ Оставить заявку',
                'Button Service price' => '💵 Стоимость услуг',
                'Button Settings' => '⚙️ Настройки',
                'Cart' => 'Корзина',
                'Catalog' => 'Каталог',
                'Checkout' => 'Оформить заказ',
                'Choose apartment' => 'Выберите квартиру',
                'Choose floor' => 'Выберите этаж',
                'Choose premises' => 'Выберите помещение',
                'Choose quantity' => 'Выберите количество',
                'Choose or write delivery method' => 'Выберите или напишите способ доставки',
                'Choose setting' => 'Выберите настройку',
                'Commercial real estate' => 'Коммерческая недвижимость',
                'Contact phone number' => 'Номер телефона для контакта',
                'Cost' => 'Стоимость',
                'Currency sum' => 'сум',
                'Delivery' => 'Доставка',
                'Delivery self pickup' => 'Самовывоз',
                'Enter delivery address or send location' => 'Напишите адрес доставки или отправьте локацию',
                'Enter new address' => 'Введите новый адрес',
                'Enter new phone number' => 'Введите новый номер телефона',
                'Get price' => 'Узнать цену',
                'in format' => 'в формате %s',
                'Last orders list' => 'Список последних заказов',
                'Location' => 'Локация',
                'Menu' => 'Меню',
                'Name' => 'Имя',
                'Name of badges' => 'Наименование нагрудных знаков',
                'No information' => 'Нет информации',
                'No more products in the category. Return' => 'В этой категории товаров нет. Вернуться назад',
                'No more products. Return last page' => 'Больше товаров нет. Вернуться на пред. стр.',
                'No products in cart' => 'Корзина пуста',
                'New order' => 'Новый заказ',
                'New request' => 'Новый запрос',
                'Order accepted' => 'Заказ принят',
                'Order cancelled' => 'Заказ отменен',
                'Order details' => 'Детали заказа',
                'Order ID' => 'Номер заказа',
                'Show products' => 'Показать товары',
                'Phone number' => 'Номер телефона',
                'Phone number saved' => 'Номер телефона сохранен',
                'Premises' => 'Помещение',
                'Price' => 'Цена',
                'Product' => 'Товар',
                'Products' => 'Товары',
                'Quantity' => 'Количество',
                'Request accepted' => 'Запрос принят',
                'Request details' => 'Детали запроса',
                'Residential real estate' => 'Жилая недвижимость',
                'Reviews' => 'Отзывы',
                'To Order' => 'Заказать',
                'To Send location' => 'Отправить локацию',
                'Total' => 'Итого',
                'Welcome' => 'Добро пожаловать!',
                'Your address' => 'Ваш адрес',
                'Your name' => 'Ваше имя',
                'Your firstname' => 'Ваше имя',
                'Your lastname' => 'Ваша фамилия',
                'Your phone number' => 'Ваш номер телефона',
                '' => '',
            ],
            'uz' => [
                'Add to cart' => 'Savatga qoʻshish',
                'Address' => 'Manzil',
                'Address saved' => 'Manzil saqlandi',
                'Apartment' => 'Kvartira',
                'Back' => 'Orqaga',
                'Button Back' => '⬅️ Orqaga',
                'Button Cancel' => '✖️ Bekor qilish',
                'Button Cart' => '🛒 Savat',
                'Button Cart Clear' => '❌ Savatni tozalash',
                'Button Catalog' => '📂 Katalog',
                'Button Change address' => '🏡 Manzil',
                'Button Change language' => '🏳️ Til',
                'Button Change phone number' => '📞 Telefon raqami',
                'Button Commercial real estate' => '🏬 Tijorat koʻchmas mulki',
                'Button Computers' => '🖥 Kompyuterlar',
                'Button Confirm' => '✔️ Tasdiqlash',
                'Button Free delivery' => '🚗 Yetkazib berish bepul',
                'Button Help' => '❓ Yordam',
                'Button Home' => '🏠 Bosh sahifa',
                'Button Info Aparto' => '📋 Info Aparto',
                'Button Laptops' => '💻 Noutbuklar',
                'Button Monoblocks' => '🖥 Monobloklar',
                'Button Orders' => '🥡 Buyurtmalar',
                'Button Our products' => '🏅 Bizning mahsulotlarimiz',
                'Button Residential real estate' => '🏢 Turar-joy majmuasi',
                'Button Reviews' => '💬 Tavsiyalar',
                'Button Send message' => '✉️Xabar yuborish',
                'Button Send my number' => 'Telefon raqamimni yuborish',
                'Button Send request' => '✏️ Soʻrov yuborish',
                'Button Service price' => '💵 Xizmatlar narxi',
                'Button Settings' => '⚙️ Sozlamalar',
                'Cart' => 'Savat',
                'Catalog' => 'Katalog',
                'Checkout' => 'Buyurtmani rasmiylashtirish',
                'Choose apartment' => 'Kvartirani tanlang',
                'Choose floor' => 'Qavatni tanlang',
                'Choose premises' => 'Joyni tanlang',
                'Choose quantity' => 'Miqdorini tanlang',
                'Choose or write delivery method' => 'Yetkazib berish usulini tanlang yoki yozing',
                'Choose setting' => 'Sozlamani tanlang',
                'Commercial real estate' => 'Tijorat koʻchmas mulki',
                'Contact phone number' => 'Aloqa uchun telefon raqami',
                'Cost' => 'Narxi',
                'Currency sum' => 'soʻm',
                'Delivery' => 'Yetkazib berish',
                'Delivery self pickup' => 'Oʻzim olib ketaman',
                'Enter delivery address or send location' => 'Yetkazib berish manzilini kiriting yoki lokatsiya yuboring',
                'Enter new address' => 'Yangi manzilni kiriting',
                'Enter new phone number' => 'Yangi telefon raqamini kiriting',
                'Get price' => 'Narxini bilish',
                'in format' => '%s formatda',
                'Last orders list' => 'Oxirgi buyurtmalaringiz roʻyxati',
                'Location' => 'Lokatsiya',
                'Menu' => 'Menyu',
                'Name' => 'Ismi',
                'Name of badges' => 'Наименование нагрудных знаков',
                'No information' => 'Ma\'lumot yoʻq',
                'No more products in the category. Return' => 'Bu kategoriyada boshqa mahsulot yoʻq. Orqaga qaytish',
                'No more products. Return last page' => 'Boshqa mahsulot yoʻq. Oxirgi sahifaga qaytish',
                'No products in cart' => 'Savat boʻsh',
                'New order' => 'Yangi buyurtma',
                'New request' => 'Yangi soʻrov',
                'Order accepted' => 'Buyurtma qabul qilindi',
                'Order cancelled' => 'Buyurtma bekor qilindi',
                'Order details' => 'Buyurtma tafsilotlari',
                'Order ID' => 'Buyurtma raqami',
                'Phone number' => 'Telefon raqami',
                'Phone number saved' => 'Telefon raqami saqlandi',
                'Premises' => 'Bino/Joy',
                'Price' => 'Narxi',
                'Product' => 'Mahsulot',
                'Products' => 'Mahsulotlar',
                'Quantity' => 'Miqdori',
                'Request accepted' => 'Soʻrov qabul qilindi',
                'Request details' => 'Soʻrov tafsilotlari',
                'Residential real estate' => 'Turar-joy majmuasi',
                'Reviews' => 'Tavsiyalar',
                'To Order' => 'Buyurtma berish',
                'To Send location' => 'Lokatsiya yuborish',
                'Total' => 'Jami',
                'Welcome' => 'Xush kelibsiz!',
                'Show products' => 'Mahsulotlarni koʻrish',
                'Your address' => 'Sizning manzilingiz',
                'Your name' => 'Ismingiz',
                'Your firstname' => 'Ismingiz',
                'Your lastname' => 'Familiyangiz',
                'Your phone number' => 'Sizning telefon raqamingiz',
                '' => '',
            ],
        ];
    }

}
