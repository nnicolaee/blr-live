<?php

declare(strict_types=1);

namespace BLRLive\Models;

/*

create table Stages (
	name varchar(50) primary key
);

*/

class Stage {
	public readonly string name;

	public function getMatches() : array {
		throw new Exception('Not implemented');
	}

	public function getScoreboard() : array {
		throw new Exception('Not implemented');
	}
}
