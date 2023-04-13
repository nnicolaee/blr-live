<?php

declare(strict_types=1);

namespace BLRLive\Controllers;

use Slim\Http\Response as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

use BLRLive\Config;
use BLRLive\Models\Team;
use Slim\Exception\{ HttpNotFoundException, HttpBadRequestException };

use BLRLive\REST\{ Controller, HttpRoute };

#[Controller('/teams')]
class TeamController {
	#[HttpRoute('POST')]
	public static function createTeam(Request $req, Response $res) {
		$data = $req->getParsedBody();
		if(!is_string($data['username']) || !is_string($data['name']))
			throw new HttpBadRequestException("Invalid fields");

		if($team = Team::get($data['username'])) // Prone to a race condition but nothing mega bad should happen
			$res = $res->withStatus(303);
		else {
			$team = Team::create(
				username: $data['username'],
				name: $data['name']
			);
			$res = $res->withStatus(201);
		}

		return $res->withHeader('Location', TeamController::teamUrl($team));
	}

	#[HttpRoute('GET', '/{username}')]
	public static function getTeam(Request $req, Response $res, $args) {
		$team = Team::get($args['username']) or throw new HttpNotFoundException($req, "Team not found");
		return $res->withJson($team);
	}

	#[HttpRoute('GET')]
	public static function getPaginatedTeams(Request $req, Response $res) {
		$total = Team::getTotal();

		$params = $req->getQueryParams();
		
		$limit  = intval($params['limit'] ?? 50);
		if($limit < 1 || $limit > 50)
			throw new HttpBadRequestException($req, 'Invalid limit parameter');

		$offset = intval($params['offset'] ?? 0);
		if($offset < 0)
			throw new HttpBadRequestException($req, 'Invalid offset parameter');

		$items = Team::getPaginated($offset, $limit);
		$count = count($items);

		$prev = null;
		if($offset > 0 && $offset - $limit < $total) { // build prev URL
			$prev_offset = max(0, $offset - $limit);
			$prev = Config::API_BASE_URL . '/teams?offset=' . $prev_offset . '&limit=' . $limit;
		}

		$next = null;
		if($offset + $count < $total) { // build next URL
			$next_offset = $offset + $count;
			$next = Config::API_BASE_URL . '/teams?offset=' . $next_offset . '&limit=' . $limit;
		}

		$first = Config::API_BASE_URL . '/teams?offset=0&limit=' . $limit;

		$last_offset = floor($total / $limit) * $limit;
		$last = Config::API_BASE_URL . '/teams?offset=' . $last_offset . '&limit=' . $limit;

		return $res->withJson([
			'total' => $total,
			'offset' => $offset,
			'count' => $count,
			'first' => $first,
			'next' => $next,
			'prev' => $prev,
			'last' => $last,
			'items' => $items
		]);
	}

	#[HttpRoute('PUT', '/{username}')]
	public static function updateTeam(Request $req, Response $res, $args) {
		$data = $req->getParsedBody();
		$team = Team::get($args['username']) or throw new HttpNotFoundException($req, "Team not found");

		if(is_string($data['name'])) $team->name = $data['name'];

		$team->save();
		return $res->withStatus(200)->withJson(TeamController::teamJson($team));
	}

	#[HttpRoute('DELETE', '/{username}')]
	public static function deleteTeam(Request $req, Response $res, $args) {
		$team = Team::get($args['username']) or throw new HttpNotFoundException($req, "Team not found");
		$team->delete();
		return $res->withStatus(204);
	}
}
