create table CurrentCompetitionStatus (
	stage varchar(50) references Stages(name) not null,
	match int references Matches(id) not null,
	livestream_url varchar(50)
);

create table Stages (
	name varchar(50) primary key
);

create table Teams (
	username varchar(50) primary key,
	name varchar(50) not null
);

insert into Teams(username, name) values ('test', 'Testuleano'), ('bazinga', 'Ba\' zinga!');

create table TeamStageParticipation (
	team varchar(50) references Teams(username),
	stage varchar(50) references Stages(name),

	primary key (team, stage)
);

create table Matches (
	id int primary key AUTO_INCREMENT,
	stage varchar(50) not null references Stages(name),
	team1 varchar(50) not null references Teams(username),
	team2 varchar(50) not null references Teams(username),
	status enum('upcoming', 'finished') not null
);

create table Games (
	id int primary key AUTO_INCREMENT,
	match_id int not null references Matches(id),
	status enum('team1', 'team2', 'draw') not null,
	finish_time datetime not null
);

create table BracketMatches (
	id int primary key references Matches(id),
	round int not null, /* left-to-right */
	order int not null, /* top-to-bottom */

	parent1 int references BracketMatches(id),
	parent2 int references BracketMatches(id)
);
