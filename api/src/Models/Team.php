<?php

declare(strict_types=1);

namespace BLRLive\Models;

/*

create table Teams (
	username varchar(50) primary key,
	name varchar(50) not null
);

*/

class Team {
	public readonly string $username;
	public string $name;

	public static function create(string $username, string $name) : Team {
		$team = new Team;

		$team->username = $username;
		$team->name = $name;

		$db = Database::connect();
		$q = $db->prepare('insert into Teams (username, name) values (?, ?)');
		$q->bind_param('ss', $username, $name);
		$q->execute();
		$db->commit();

		return $team;
	}

	public static function getTotal() : int {
		return Database::Connect()->query('select count(*) from Teams')->fetch_array()[0];
	}

	public static function getPaginated(int $offset = 0, int $limit = 50) : array {
		$db = Database::Connect();

		$teams = [];

		$q = $db->prepare('select username, name from Teams limit ? offset ?');
		$q->bind_param('ii', $limit, $offset);
		$q->execute();
		$r = $q->get_result();

		foreach($r as $teamRow) {
			$teams[] = Team::fromRow($teamRow);
		}

		return $teams;
	}

	public static function get(string $username) : ?Team {
		$db = Database::Connect();
		$q = $db->prepare('select * from Teams where username = ?');
		$q->bind_param('s', $username);

		$q->execute();
		$r = $q->get_result();
		
		return Team::fromRow($r->fetch_assoc());
	}

	public function save() {
		$db = Database::Connect();
		$q = $db->prepare('update Teams set name = ? where username = ?');
		$q->bind_param('ss', $this->name, $this->username);
		$q->execute();
		$db->commit();
	}

	public function delete() {
		$db = Database::Connect();
		$q = $db->prepare('delete from Teams where username = ?');
		$q->bind_param('s', $this->username);
		$q->execute();
		$db->commit();
	}

	protected static function fromRow($row) : ?Team {
		if(!$row) return null;

		$team = new Team;
		$team->username = $row['username'];
		$team->name = $row['name'];

		return $team;
	}
}
