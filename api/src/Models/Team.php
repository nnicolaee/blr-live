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
    protected static string $baseUrl = \BLRLive\Config::API_BASE_URL . "/teams";

    public readonly string $username;
    public string $name;

    public static function create(string $username, string $name): Team
    {
        $team = new Team();

        $team->username = $username;
        $team->name = $name;

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

    public static function getPaginated(int $offset = 0, int $limit = 50): array
    {
        $db = Database::Connect();
        $r = $db->execute_query('select username, name from Teams limit ? offset ?', [$limit, $offset]);

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

    public function save()
    {
        $db = Database::Connect();
        $db->execute_query('update Teams set name = ? where username = ?', [$this->name, $this->username]);
        $db->commit();
    }

    public function delete()
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

        $team = new Team();
        $team->username = $row['username'];
        $team->name = $row['name'];

        return $team;
    }

    public function jsonSerialize(): array
    {
        return [
            'self' => $this->getUrl(),
            'username' => $this->username,
            'name' => $this->name
        ];
    }
}
