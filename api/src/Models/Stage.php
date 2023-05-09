<?php

declare(strict_types=1);

namespace BLRLive\Models;

/*

create table Stages (
    name varchar(50) primary key,
    bracket int null,

    foreign key (bracket) references BracketSlots(id) on delete set null
);

create table TeamStageParticipation (
    stage varchar(50) not null,
    team varchar(50) not null,
    status enum('participant', 'qualified', 'disqualified') not null default 'participant',

    foreign key (stage) references Stages(name),
    foreign key (team) referenes Teams(username)
);

*/

class Stage extends BaseModel
{
    protected static string $baseUrl = \BLRLive\Config::API_BASE_URL . "/stages";

    public function __construct(
        public readonly string $name,
        public ?string $bracket
    ) {
    }

    public function getId()
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
        $stage = $db->execute_query(
            'select name, bracket from Stages where name = ?',
            [$name]
        )->fetch_assoc();
        return Stage::fromRow($stage);
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

        $r = $db->execute_query('select name, bracket from Stages');
        $stages = [];
        foreach ($r as $row) {
            $stage = Stage::fromRow($row);
            $stage->brief = $brief;
            $stages[] = $stage;
        }
        return $stages;
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
