<?php

declare(strict_types=1);

namespace BLRLive\Models;

use \BLRLive\Schemas\ScoreboardLine;

/*

create table Stages (
    name varchar(50) primary key,
    bracket int null,

    foreign key (bracket) references BracketSlots(id) on delete set null
);

*/

class Stage extends BaseModel
{
    protected bool $brief = false;

    public function __construct(
        public /*readonly*/ string $name,
        public ?int $bracket
    ) {
    }

    public function getId(): string
    {
        return $this->name;
    }

    private static function fromRow($row): ?Stage
    {
        if (!$row) {
            return null;
        }

        return new Stage(
            name: $row['name'],
            bracket: $row['bracket']
        );
    }

    public static function get(string $name): ?Stage
    {
        return Stage::fromRow(Database::execute_query(
            'select * from Stages where name = ?',
            [$name]
        )->fetch_assoc());
    }

    public static function exists(string $name): bool
    {
        return !is_null(Database::execute_query(
            'select name from Stages where name = ?',
            [$name]
        )->fetch_assoc());
    }

    public static function create(string $name): Stage
    {
        $stage = new Stage(
            name: $name,
            bracket: null
        );

        $db = Database::connect();
        Database::execute_query('insert into Stages (name) values (?)', [$name], $db);
        $db->commit();

        return $stage;
    }

    public function delete(): void
    {
        $db = Database::connect();
        Database::execute_query('delete from Stages where name = ?', [$this->name], $db);
        $db->commit();
    }

    public static function getAll(bool $brief = true): array
    {
        $r = Database::execute_query('select * from Stages order by name');
        $stages = [];
        foreach ($r as $row) {
            $stage = Stage::fromRow($row);
            $stage->brief = $brief;
            $stages[] = $stage;
        }
        return $stages;
    }

    public static function getHavingBracket(int $bracket_id): ?Stage
    {
        return Stage::fromRow(Database::execute_query(
            'select * from Stages where bracket = ?',
            [$bracket_id]
        )->fetch_assoc());
    }

    public static function addTeam(string $stage, string $team)
    {
        $db = Database::connect();
        Database::execute_query('insert into TeamStageParticipation (stage, team) values (?, ?)', [$stage, $team], $db);
        $db->commit();
    }

    public static function removeTeam(string $stage, string $team)
    {
        $db = Database::connect();
        Database::execute_query('insert into Stages (name) values (?)', [$name], $db);
        $db->commit();
    }

    public function getScoreboard() : array
    {
        $pars = Participation::getForStage($this->name);
        $scoreboard = [];
        $db = Database::connect();
        foreach($pars as $par) {
            $w = Database::execute_query('select count(*) from Matches where stage = ? and ((team1 = ? and status = \'win1\') or (team2 = ? and status = \'win2\'))', [$this->name, $par->team, $par->team])->fetch_array()[0];
            $d = Database::execute_query('select count(*) from Matches where stage = ? and ((team1 = ? or team2 = ?) and status = \'draw\')', [$this->name, $par->team, $par->team])->fetch_array()[0];
            $l = Database::execute_query('select count(*) from Matches where stage = ? and ((team1 = ? and status = \'win2\') or (team2 = ? and status = \'win1\'))', [$this->name, $par->team, $par->team])->fetch_array()[0];

            $gw = Database::execute_query('select count(*) from Games join Matches on Games.match_id = Matches.id where stage = ? and ((team1 = ? and Games.status = \'team1\') or (team2 = ? and Games.status = \'team2\'))', [$this->name, $par->team, $par->team])->fetch_array()[0];
            $gl = Database::execute_query('select count(*) from Games join Matches on Games.match_id = Matches.id where stage = ? and ((team1 = ? and Games.status = \'team2\') or (team2 = ? and Games.status = \'team1\'))', [$this->name, $par->team, $par->team])->fetch_array()[0];

            $scoreboard[] = new ScoreboardLine(
                team: Team::get($par->team)->jsonSerialize(),
                wins: $w,
                draws: $d,
                losses: $l,
                score: 3*$w + $d,
                tiebreaker: $gw - $gl,
                status: $par->status
            );
        }

        return $scoreboard;
    }

    public function jsonSerialize(): \BLRLive\Schemas\StageBrief|\BLRLive\Schemas\Stage
    {
        if ($this->brief) {
            return new \BLRLive\Schemas\StageBrief(
                name: $this->name,
                bracket: $this->bracket
            );
        } else {
            return new \BLRLive\Schemas\Stage(
                name: $this->name,
                bracket: $this->bracket,
                scoreboard: $this->getScoreboard(),
                matches: []
            );
        }
    }
}
