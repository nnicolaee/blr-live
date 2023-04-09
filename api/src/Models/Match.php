<?php

namespace BLRLive\Models;

/*

create table Matches (
	id int primary key AUTO_INCREMENT,
	stage varchar(50) not null references Stages(name),
	team1 varchar(50) null references Teams(username),
	team2 varchar(50) null references Teams(username),
	status enum('upcoming', 'finished') not null,
	kind enum('playoff', 'bracket')
);

create table Games (
	id int primary key AUTO_INCREMENT,
	match_id int not null references Matches(id),
	status enum('team1', 'team2', 'draw') not null,
	finish_time datetime not null
);

*/

class Match {
	public readonly int $id;
	public string $stage;
	public string $team1, $team2;
	public int $score1, $score2;
	public string $status;

	
}
