<?php

declare(strict_types=1);

namespace BLRLive\Models;

/*

create table CurrentCompetitionStatus (
	stage varchar(50) references Stages(name) not null,
	match int references Matches(id) not null,
	livestream_url varchar(50)
);

*/

class CurrentStatus {
	public string $stage;
	public int $match;
	public string $livestream;

	public static function get() : CurrentStatus {
		$db = Database::connect();
		[ $stage, $match, $livestream ] = $db->query('select stage, match, livestream_url from CurrentCompetitionStatus')->fetch_array();
		$db->close();

		$currentStatus = new CurrentStatus;
		$currentStatus->stage = $stage;
		$currentStatus->match = $match;
		$currentStatus->livestream = $livestream;
		return $currentStatus;
	}

	public function save() {
		$db = Database::connect();
		$stmt = $db->prepare('update CurrentStatus set stage = ?, match = ?, livestream_url = ?');
		$stmt->bind_param('sis', $this->stage, $this->match, $this->livestream);
		$stmt->execute();
	}
}
