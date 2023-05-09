<?php

declare(strict_types=1);

namespace BLRLive\Models;

use BLRLive\Config;

class Database
{
    private static ?\mysqli $db = null;

    public static function connect(): \mysqli
    {
        if (!is_null(static::$db)) {
            return static::$db;
        }

        static::$db = new \mysqli(Config::DB_HOSTNAME, Config::DB_USERNAME, Config::DB_PASSWORD, Config::DB_DATABASE);

        if (!static::$db) {
            throw new Exception('Could not connect to database', 500);
        }

        static::$db->autocommit(false);
        static::$db->begin_transaction();

        return static::$db;
    }
}
