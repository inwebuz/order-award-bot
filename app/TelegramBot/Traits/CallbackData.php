<?php


namespace App\TelegramBot\Traits;


use Longman\TelegramBot\Commands\UserCommands\CatalogueCommand;
use Longman\TelegramBot\Commands\UserCommands\OrderCommand;
use Longman\TelegramBot\Commands\UserCommands\ProductCommand;

trait CallbackData
{
    public $keyCommands = [
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
        'order_product' => [
            'command' => 'order',
            'class' => OrderCommand::class
        ],
    ];

    public function parseCallbackData($callback_data)
    {
        $data = [];
        $callback_data = explode('|', $callback_data);
        foreach ($callback_data as $key => $value) {
            $row = explode(':', $value);
            $data[$row[0]] = isset($row[1]) ? $row[1] : '';
        }
        return $data;
    }

    public function getCallbackCommand($data)
    {
        $command = [];
        $data = $this->parseCallbackData($data);
        foreach ($this->keyCommands as $key => $value) {
            if (isset($data[$key])) {
                $command = $value;
                break;
            }
        }
        return $command;
    }
}
