<?php

declare(strict_types=1);

namespace BLRLive\Models;

/*

create table BracketSlots (
    id int primary key AUTO_INCREMENT,
    match_id int null,
    parent int null,

    foreign key (match_id) references Matches(id) on delete set null,
    foreign key (parent) references BracketSlots(id) on delete cascade
);

*/

class Bracket extends BaseModel
{
    private const MAX_DEPTH = 6; // Shouldn't ever have more than 64 teams / 6 rounds in the same bracket

    public function __construct(
        public /*readonly*/ int $id,
        public ?int $match,
        public /*readonly*/ ?int $parent,
        public /*readonly*/ array $children
    ) {
    }

    public static function getBracket(int $id): ?Bracket
    {
        //$db = Database::connect();

        $r = Database::execute_query(
            'select match_id from BracketSlots where id = ? and parent is null',
            [$id]
        )->fetch_assoc();
        if (!$r) {
            return null;
        }

        return new Bracket(
            id: $id,
            match: $r['match_id'],
            parent: null,
            children: Bracket::getTree($id/*, $db*/)
        );
    }

    public static function getSlot(int $id): ?Bracket
    {
        //$db = Database::connect();

        $r = Database::execute_query(
            'select match_id, parent from BracketSlots where id = ?',
            [$id]
        )->fetch_assoc();
        if (!$r) {
            return null;
        }

        return new Bracket(
            id: $id,
            match: $r['match_id'],
            parent: $r['parent'],
            children: []
        );
    }

    public static function get(string $id): ?Bracket
    {
        if (!is_numeric($id)) {
            return null;
        }
        return getSlot(intval($id));
    }

    private static function getTree(int $parent_id, /*\mysqli $db, */int $depth = 0): array
    {
        if ($depth > Bracket::MAX_DEPTH) {
            throw new RuntimeException('Bracket too deep, might have cycles');
        }

        $r = Database::execute_query(
            'select id, match_id from BracketSlots where parent = ?',
            [$parent_id]
        );
        $children = [];
        foreach ($r as $row) {
            $children[] = new Bracket(
                id: $row['id'],
                match: $row['match_id'],
                parent: $parent_id,
                children: Bracket::getTree($row['id'], /*$db,*/ $depth + 1)
            );
        }
        return $children;
    }

    public function save(): void
    {
        $db = Database::connect();
        Database::execute_query(
            'update BracketSlots set match_id = ? where id = ?',
            [$this->match, $this->id],
            $db
        );
        $db->commit();
    }

    public function delete(): void
    {
        $db = Database::connect();
        Database::execute_query(
            'delete from BracketSlots where id = ?',
            [$this->id],
            $db
        );
        $db->commit();
    }

    public static function createTree(
        int $depth,
        ?int $parent = null
    ): Bracket {
        if ($depth < 0) {
            throw new RuntimeException('Bracket depth cannot be negative');
        }
        if ($depth > Bracket::MAX_DEPTH) {
            throw new RuntimeException('Bracket too deep');
        }

        $db = Database::connect();
        Database::execute_query(
            'insert into BracketSlots(match_id, parent) values (null, ?)',
            [$parent],
            $db
        );
        $id = $db->insert_id;
        $db->commit();

        return new Bracket(
            id: $id,
            match: null,
            parent: $parent,
            children: $depth > 1 ? [
                Bracket::createTree($depth - 1, $id),
                Bracket::createTree($depth - 1, $id)
            ] : []
        );
    }

    public function getStage() : ?string
    {
        $bracket = $this->id;
        for($depth = 10; $bracket && $depth > 0; $depth--) {
            $row = Database::execute_query(
                'select name from Stages where bracket = ?',
                [$bracket_id]
            )->fetch_array();
            if($row) return $row[0];

            $bracket = Bracket::getSlot($bracket)->parent;
        }

        return null;
    }

    public function jsonSerialize(): \BLRLive\Schemas\BracketNode
    {
        $children = [];
        foreach ($this->children as $child) {
            $children[] = $child->jsonSerialize();
        }

        return new \BLRLive\Schemas\BracketNode(
            id: $this->id,
            match: $this->match,
            children: $children
        );
    }
}
