<?php

declare(strict_types=1);

namespace BLRLive\Models;

use BLRLive\Config;

class Database {
	public static function Connect() : \mysqli {
		$db = new \mysqli(Config::DB_HOSTNAME, Config::DB_USERNAME, Config::DB_PASSWORD, Config::DB_DATABASE);

		if($db == false) throw new Exception('Could not connect to database', 500);

		$db->autocommit(false);
		$db->begin_transaction();

		return $db;
	}
}
