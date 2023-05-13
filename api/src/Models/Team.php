<?php

declare(strict_types=1);

namespace BLRLive\Models;

/*

create table Teams (
    username varchar(50) primary key,
    name varchar(50) not null
);

*/

class Team extends BaseModel
{
    protected static string $baseUrl = "teams";

    public function __construct(
        public readonly string $username,
        public string $name
    ) {
    }

    public function getId(): string
    {
        return $this->username;
    }

    public static function create(string $username, string $name): Team
    {
        $team = new Team(
            username: $username,
            name: $name
        );

        $db = Database::connect();
        $db->execute_query('insert into Teams (username, name) values (?, ?)', [$username, $name]);
        $db->commit();

        return $team;
    }

    public static function getTotal(): int
    {
        $db = Database::Connect();
        return intval($db->query('select count(*) from Teams')->fetch_array()[0]);
    }

    public static function getPaginated(int $offset = 0, int $limit = 50): array // of Team
    {
        $db = Database::Connect();
        $r = $db->execute_query('select username, name from Teams order by name limit ? offset ?', [$limit, $offset]);

        $teams = [];
        foreach ($r as $teamRow) {
            $teams[] = Team::fromRow($teamRow);
        }

        return $teams;
    }

    public static function getAll() : array
    {
        $db = Database::connect();

        $r = $db->execute_query('select username, name from Teams order by name', [$limit, $offset]);
        $teams = [];
        foreach ($r as $teamRow) {
            $teams[] = Team::fromRow($teamRow);
        }

        return $teams;
    }

    public static function get(string $username): ?Team
    {
        $db = Database::Connect();
        $r = $db->execute_query('select * from Teams where username = ?', [$username]);

        return Team::fromRow($r->fetch_assoc());
    }

    public static function exists(string $username): bool
    {
        $db = Database::connect();
        return !is_null($db->execute_query(
            'select username from Teams where username = ?',
            [$username]
        )->fetch_assoc());
    }

    public function save(): void
    {
        $db = Database::Connect();
        $db->execute_query('update Teams set name = ? where username = ?', [$this->name, $this->username]);
        $db->commit();
    }

    public function delete(): void
    {
        $db = Database::Connect();
        $db->execute_query('delete from Teams where username = ?', [$this->username]);
        $db->commit();
    }

    protected static function fromRow($row): ?Team
    {
        if (!$row) {
            return null;
        }

        return new Team(
            username: $row['username'],
            name: $row['name']
        );
    }

    public function jsonSerialize(): \BLRLive\Schemas\Team
    {
        return new \BLRLive\Schemas\Team(
            username: $this->username,
            name: $this->name
        );
    }
}
