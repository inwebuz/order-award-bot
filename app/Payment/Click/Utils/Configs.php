<?php

namespace App\Payment\Click\Utils;

/**
 * @name Configs class
 */
class Configs
{
    /** @var configs array-like */
    private $configs;

    /**
     * Configs constructor
     */
    public function __construct()
    {
        $this->configs = config('click');
    }

    /**
     * @name get_provider_configs method
     * @return array-like
     */
    public function get_provider_configs()
    {
        return $this->configs['provider'];
    }

    /**
     * @name get_database_configs method
     * @return array-like
     */
    public function get_database_configs()
    {
        return [
            'dsn' => 'mysql:host=localhost;dbname=' . config('database.connections.mysql.database'),
            'username' => config('database.connections.mysql.username'),
            'password' => config('database.connections.mysql.password'),
        ];
    }



    /**
     * @name get_endpoint method
     * @return string
     */
    public function get_endpoint()
    {
        return $this->configs['endpoint'];
    }


}
