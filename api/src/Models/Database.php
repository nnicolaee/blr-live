<?php

declare(strict_types=1);

namespace BLRLive\Models;

use BLRLive\Config;

class Database {
	public static function connect(bool $transaction = true) : \mysqli {
		$db = new \mysqli(Config::DB_HOSTNAME, Config::DB_USERNAME, Config::DB_PASSWORD, Config::DB_DATABASE);

		if(!$db) throw new Exception('Could not connect to database', 500);

		if($transaction)
		{
			$db->autocommit(false);
			$db->begin_transaction();
		}

		return $db;
	}
}
