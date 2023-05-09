<?php

declare(strict_types=1);

namespace BLRLive\Models;

/*

create table Stages (
    name varchar(50) primary key,
    bracket int null,

    foreign key (bracket) references BracketSlots(id) on delete set null
);

*/

class Stage extends BaseModel
{
    protected static string $baseUrl = "stages";
    protected bool $brief = false;

    public function __construct(
        public readonly string $name,
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
        $db = Database::connect();
        return Stage::fromRow($db->execute_query(
            'select * from Stages where name = ?',
            [$name]
        )->fetch_assoc());
    }

    public static function exists(string $name): bool
    {
        $db = Database::connect();
        return !is_null($db->execute_query(
            'select name from Stages where name = ?',
            [$name]
        )->fetch_assoc());
    }

    public static function create(string $name): Stage
    {
        $stage = new Stage(
            name: $name
        );

        $db = Database::connect();
        $db->execute_query('insert into Stages (name) values (?)', [$name]);
        $db->commit();

        return $stage;
    }

    public static function getAll(bool $brief = true): array
    {
        $db = Database::connect();

        $r = $db->execute_query('select * from Stages');
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
        $db = Database::connect();

        return Stage::fromRow($db->execute_query(
            'select * from Stages where bracket = ?',
            [$bracket_id]
        )->fetch_assoc());
    }

    public static function addTeam(string $stage, string $team)
    {

        $db = Database::connect();
        $db->execute_query('insert into TeamStageParticipation (stage, team) values (?, ?)', [$stage, $team]);

        $teams = $db->execute_query('select team from TeamStageParticipation where team = ?', [$team]);
        foreach($teams as $t)
        {
            var_dump($t);
        }

        $db->commit();
    }

    public static function removeTeam(string $stage, string $team)
    {

        $db = Database::connect();
        $db->execute_query('insert into Stages (name) values (?)', [$name]);
        $db->commit();
    }

    public function jsonSerialize(): \BLRLive\Schemas\StageBrief|\BLRLive\Schemas\Stage
    {
        if ($this->brief) {
            return new \BLRLive\Schemas\StageBrief(
                id: $this->id,
                name: $this->name,
                bracket: $this->bracket
            );
        } else {
            return new \BLRLive\Schemas\Stage(
                id: $this->id,
                name: $this->name,
                bracket: $this->bracket,
                scoreboard: [],
                matches: []
            );
        }
    }
}
