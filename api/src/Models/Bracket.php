<?php

declare(strict_types=1);

namespace BLRLive\Models;

/*

create table BracketSlots (
	id int primary key AUTO_INCREMENT,
	match_id int null,
	parent int null,

	foreign key (match_id) references Matches(id) on delete set null,
	foreign key (parent) references BracketSlot(id) on delete cascade
);

*/

class Bracket extends BaseModel {
    protected static string $baseUrl = \BLRLive\Config::API_BASE_URL . "/brackets";

	public readonly int $id;
	public ?int $match;
	public readonly ?Bracket $parent;
	public readonly array $children;

	const MAX_DEPTH = 6; // Shouldn't ever have more than 64 teams / 6 rounds in the same bracket

	public static function getBracket(int $id) : ?Bracket
	{
		$db = Database::connect();

		$bracket = new Bracket;

		$r = $db->execute_query('select match_id from BracketSlots where id = ? and parent is null', [$id])->fetch_assoc();
		if(!$r) return null;
		$bracket->match = $r['match_id'];
		$bracket->parent = null;
		$bracket->id = $id;
		$bracket->children = Bracket::getTree($bracket, $db);

		return $bracket;
	}

	public static function getSlot(int $id) : ?Bracket
	{
		$db = Database::connect();

		$bracket = new Bracket;

		$r = $db->execute_query('select match_id, parent from BracketSlots where id = ?', [$id])->fetch_assoc();
		if(!$r) return null;
		$bracket->match = $r['match_id']; // only this is really required, because getSlot should only be used for update and delete
		$bracket->parent = null; // not really
		$bracket->id = $id;
		$bracket->children = [];

		return $bracket;
	}

	public static function get(string $id) : ?Bracket
	{
		if(!is_numeric($id)) return null;
		return getSlot(intval($id));
	}

	private static function getTree(Bracket $parent, \mysqli $db, int $depth = 0) : array
	{
		if($depth > Bracket::MAX_DEPTH) throw new RuntimeException('Bracket too deep, might have cycles');

		$r = $db->execute_query('select id, match_id from BracketSlots where parent = ?', [$parent->id]);
		$children = [];
		foreach($r as $row)
		{
			$child = new Bracket;
			$child->id = $row['id'];
			$child->match = $row['match_id'];
			$child->parent = $parent;
			$child->children = Bracket::getTree($child, $db, $depth + 1);

			$children[] = $child;
		}
		return $children;
	}

	public function save() : void
	{
		$db = Database::connect();
		$db->execute_query('update BracketSlots set match_id = ? where id = ?', [$this->match, $this->id]);
		$db->commit();
	}

	public function delete() : void
	{
		$db = Database::connect();
		$db->execute_query('delete from BracketSlots where id = ?', [$this->id]); // should cascade to children without effort
		$db->commit();
	}

	public static function createTree(int $depth, ?Bracket $parent = null, ?\mysqli $db = null) : Bracket
	{
		if($depth < 0) throw new RuntimeException('Bracket depth cannot be negative');
		if($depth > Bracket::MAX_DEPTH) throw new RuntimeException('Bracket too deep');

		if(!$db) $db = Database::connect();
		$db->execute_query('insert into BracketSlots(match_id, parent) values (null, ?)', [$parent?->id]);
		$id = $db->insert_id;
		$db->commit();
		
		$bracket = new Bracket;
		$bracket->id = $id;
		$bracket->match = null;
		$bracket->parent = $parent;

		$bracket->children = $depth > 1 ? [
			Bracket::createTree($depth - 1, $bracket, $db),
			Bracket::createTree($depth - 1, $bracket, $db)
		] : [];

		return $bracket;
	}

	public function jsonSerialize() : array
	{
		return [
			'self' => $this->getUrl(),
			'matches' => $this->matchesJson()
		];
	}

	private function matchesJson() : array {
		$children = [];
		foreach($this->children as $child)
		{
			$children[] = $child->matchesJson();
		}

		return [
			'self' => $this->getUrl(),
			'match' => $this->match ? MMatch::get($this->match)?->getUrl() : null,
			'children' => $children
		];
	}
}
