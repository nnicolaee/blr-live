<?php

namespace BLRLive\Models;

/*

create table BracketMatches (
	id int primary key references Matches(id),
	round int not null, / * left-to-right * /
	order int not null, / * top-to-bottom * /

	parent1 int references BracketMatches(id),
	parent2 int references BracketMatches(id)
);

*/

class BracketMatch extends Match {
	
}
