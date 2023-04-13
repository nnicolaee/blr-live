<?php

declare(strict_types=1);

namespace BLRLive\Models;

/*

create table Matches (
	id int primary key AUTO_INCREMENT,
	stage varchar(50) not null,
	team1 varchar(50) not null,
	team2 varchar(50) not null,
	status enum('upcoming', 'finished') not null,

	foreign key (stage) references Stages(name) on delete cascade,
	foreign key (team1) references Teams(username) on delete cascade,
	foreign key (team2) references Teams(username) on delete cascade
);

create table Games (
	id int primary key AUTO_INCREMENT,
	match_id int not null,
	status enum('team1', 'team2', 'draw') not null,
	finish_time datetime not null,

	foreign key (match_id) references Matches(id) on delete cascade
);

*/

class MMatch extends BaseModel { // Funny name because 'Match' clashes with the keyword :(
    protected static string $baseUrl = \BLRLive\Config::API_BASE_URL . "/matches";

	public readonly int $id;
	public string $stage;
	public string $team1, $team2;
	public int $score1, $score2;
	public string $status;

	public static function get(int $id) : MMatch
	{
		return new MMatch;
	}

	public function jsonSerialize() : array
	{
		return [
			'self' => $this->getUrl(),
			'stage' => $this->stage,
			'team1' => $this->team1,
			'team2' => $this->team2,
			'score1' => $this->score1,
			'score2' => $this->score2,
			'status' => $this->status
		];
	}
}
