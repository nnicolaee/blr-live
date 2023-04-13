<?php

declare(strict_types=1);

namespace BLRLive\Models;

/*

create table LiveEvents (
	id int primary key AUTO_INCREMENT,
	event varchar(50) not null,
	data varchar(256) not null
);

*/

class LiveEvents {
	private \mysqli $db;
	private array $queue;
	public int $lastId;

	public function __construct()
	{
		$this->db = Database::connect(false);
		$this->queue = [];

		$this->lastId = $this->db->query('select id from LiveEvents order by id desc limit 1')->fetch_array()[0];
	}

	private function fetch()
	{
		$r = $this->db->execute_query('select id, event, data from LiveEvents where id > ? order by id asc', [$this->lastId]);

		foreach($r as $row) {
			$this->queue[] = ['event' => $row['event'], 'data' => json_decode($row['data'])];
			$this->lastId = $row['id'];
		}
	}

	public function hasEvent()
	{
		if(empty($this->queue)) $this->fetch();

		return !empty($this->queue);
	}

	public function getEvent()
	{
		if(!$this->hasEvent()) return null;

		return array_shift($this->queue);
	}

	public static function sendEvent(string $event, mixed $data)
	{
		$db = Database::connect();
		$db->execute_query('insert into LiveEvents(event, data) values (?, ?)', [$event, json_encode($data)]);
		$db->commit();
	}
}
