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
    public function __construct(
        public readonly string $stage,
        public readonly string $team,
        public string $status
    ) {
    }

    public static function create(string $stage, string $team) : Participation
    {
        $db = Database::connect();

        $db->execute_query('insert into TeamStageParticipation (stage, team, status) values (?, ?, ?)', [$stage, $team, 'participant']);
        $db->commit();

        return new Participation(
            stage: $stage,
            team: $team,
            status: 'participant'
        );
    }

    public static function get(string $_) : ?Participation
    {
        throw new \Exception('Nono');
    }

    public static function getPar(string $stage, string $team) : ?Participation
    {
        $db = Database::connect();
        return Participation::fromRow($db->execute_query('select * from TeamStageParticipation where stage = ? and team = ?', [$stage, $team])->fetch_array());
    }

    public static function getForStage(string $stage) : array
    {
        $db = Database::connect();
        $pars = [];
        foreach($db->execute_query('select * from TeamStageParticipation where stage = ?', [$stage]) as $row) {
            $pars[] = Participation::fromRow($row);
        }
        return $pars;
    }

    public function save() : void
    {
        $db = Database::connect();
        $db->execute_query('update TeamStageParticipation set status = ? where stage = ? and team = ?', [$this->status, $this->stage, $this->team]);
        $db->commit();
    }

    public function jsonSerialize() : \BLRLive\Schemas\Participation
    {
        return new \BLRLive\Schemas\Participation(
            team: $this->team,
            stage: $this->stage,
            status: $this->status
        );
    }

    public static function fromRow(array $row) : ?Participation
    {
        if(!$row) {
            return null;
        }

        return new Participation(
            team: $row['team'],
            stage: $row['stage'],
            status: $row['status']
        );
    }

    public function delete() : void
    {
        $db = Database::connect();
        $db->execute_query('delete from TeamStageParticipation where stage = ? and team = ?', [$this->stage, $this->team]);
        $db->commit();
    }
}
