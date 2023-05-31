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

        static::$db = new \mysqli(Config::DB_HOSTNAME, Config::DB_USERNAME, Config::DB_PASSWORD, Config::DB_DATABASE);

        if (!static::$db) {
            throw new Exception('Could not connect to database', 500);
        }

        static::$db->autocommit(false);
        static::$db->begin_transaction();

        return static::$db;
    }

    public static function execute_query(string $query, array $params = [], ?\mysqli $db = null) {
        if(!$db) {
            $db = Database::connect();
        }
        // return $db-> execute_query($query, $params); // But we don't have PHP 8.2 :(

        $stmt = $db->prepare($query);
        $types = '';
        foreach($params as $param) {
            if(gettype($param) === 'integer' || gettype($param) === 'NULL') {
                $types .= 'i';
            } else if(gettype($param) === 'string') {
                $types .= 's';
            } else if(gettype($param) === 'double') {
                $types .= 'd';
            } else {
                throw new Exception('Invalid param type');
            }
        }
        if($types) $stmt->bind_param($types, ...$params);
        $stmt->execute();
        return $stmt->get_result();
    }

    public static function close()
    {
        try {
            if (isset(static::$db?->server_info)) {
                static::$db->close();
            }
        } catch(\Exception $e) {

        }
    }
}
