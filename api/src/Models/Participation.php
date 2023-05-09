<?php

declare(strict_types=1);

namespace BLRLive\Models;

/*

create table TeamStageParticipation (
    stage varchar(50) not null,
    team varchar(50) not null,
    status enum('participant', 'qualified', 'disqualified') not null default 'participant',

    foreign key (stage) references Stages(name) on delete cascade,
    foreign key (team) referenes Teams(username) on delete cascade
);

*/

class Participation extends BaseModel
{

}
