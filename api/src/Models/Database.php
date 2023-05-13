<?php

declare(strict_types=1);

namespace BLRLive\Models;

use BLRLive\Config;

class Database
{
    private static ?\mysqli $db = null;

    public static function connect(): \mysqli
    {
        try {
            if (isset(static::$db?->server_info)) {
                return static::$db;
            }
        } catch(\Exception $e) {

        }

        static::$db = new \mysqli('p:' . Config::DB_HOSTNAME, Config::DB_USERNAME, Config::DB_PASSWORD, Config::DB_DATABASE);

        if (!static::$db) {
            throw new Exception('Could not connect to database', 500);
        }

        static::$db->autocommit(false);
        static::$db->begin_transaction();

        return static::$db;
    }
}
