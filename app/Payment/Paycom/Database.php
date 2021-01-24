<?php

namespace App\Payment\Paycom;

class Database
{
    protected static $db;

    public function new_connection()
    {
        $db = null;

        // connect to the database
        $db_options = [
            \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC, // fetch rows as associative array
            \PDO::ATTR_PERSISTENT         => true // use existing connection if exists
        ];

        $db = new \PDO(
            'mysql:dbname=' . config('database.connections.mysql.database') . ';host=' . config('database.connections.mysql.host') . ';charset=utf8',
            config('database.connections.mysql.username'),
            config('database.connections.mysql.password'),
            $db_options
        );

        return $db;
    }

    /**
     * Connects to the database
     * @return null|\PDO connection
     */
    public static function db()
    {
        if (!self::$db) {
            $instance = new self();
            self::$db = $instance->new_connection();
        }
        return self::$db;
    }
}
