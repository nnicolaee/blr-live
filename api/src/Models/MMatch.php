<?php

declare(strict_types=1);

namespace BLRLive\Models;

/*

create table Matches (
    id int primary key AUTO_INCREMENT,
    stage varchar(50) not null,
    team1 varchar(50) not null,
    team2 varchar(50) not null,
    status enum('upcoming', 'win1', 'win2', 'draw') not null,

    foreign key (stage, team1) references TeamStageParticipation(stage, team) on delete cascade,
    foreign key (stage, team2) references TeamStageParticipation(stage, team) on delete cascade
);

*/

// Funny name because 'Match' clashes with the keyword :(
class MMatch extends BaseModel
{
    private int $score1 = 0;
    private int $score2 = 0;
    private array $games;

    public function __construct(
        public /*readonly*/ int $id,
        public string $stage,
        public string $team1,
        public string $team2,
        public string $status = 'upcoming'
    ) {
        $this->games = Game::getForMatch($id);

        foreach ($this->games as $game) {
            if ($game->status == 'team1') {
                $this->score1++;
            }
            if ($game->status == 'team2') {
                $this->score2++;
            }
        }
    }

    public static function create(string $stage, string $team1, string $team2): MMatch
    {
        $db = Database::connect();
        Database::execute_query(
            'insert into Matches (stage, team1, team2, status) values (?, ?, ?, ?)',
            [$stage, $team1, $team2, 'upcoming'],
            $db
        );
        $id = $db->insert_id;
        $db->commit();

        return new MMatch(
            id: $id,
            stage: $stage,
            team1: $team1,
            team2: $team2
        );
    }

    public static function get(string $id): ?MMatch
    {
        if (!is_numeric($id)) {
            return null;
        }

        $r = Database::execute_query(
            'select * from Matches where id = ?',
            [intval($id)]
        )->fetch_assoc();

        return MMatch::fromRow($r);
    }

    public static function exists(string $id): bool
    {
        if (!is_numeric($id)) {
            return false;
        }

        return !is_null(Database::execute_query(
            'select id from Matches where id = ?',
            [$id]
        )->fetch_assoc());
    }

    public function addGame(string $status): Game
    {
        if ($status != 'team1' && $status != 'team2' && $status != 'draw') {
            throw new RuntimeException('Invalid game status');
        }

        return Game::create(
            match: $this->id,
            status: $status
        );
    }

    public function delete(): void
    {
        $db = Database::connect();
        Database::execute_query(
            'delete from Matches where id = ?',
            [$this->id],
            $db
        );
        $db->commit();
    }

    public function save(): void
    {
        $db = Database::connect();
        Database::execute_query(
            'update Matches set status = ? where id = ?',
            [$this->status, $this->id],
            $db
        );
        $db->commit();
    }

    public static function fromRow(?array $row) : ?MMatch
    {
        if(!$row) {
            return null;
        }

        return new MMatch(
            id: $row['id'],
            stage: $row['stage'],
            team1: $row['team1'],
            team2: $row['team2'],
            status: $row['status']
        );
    }

    public function jsonSerialize(): \BLRLive\Schemas\MMatch
    {
        return new \BLRLive\Schemas\MMatch(
            id: $this->id,
            stage: $this->stage,
            team1: Team::get($this->team1)->jsonSerialize(),
            team2: Team::get($this->team2)->jsonSerialize(),
            score1: $this->score1,
            score2: $this->score2,
            status: $this->status,
            games: Game::getForMatch($this->id)
        );
    }
}
