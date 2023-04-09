<?php

namespace BLRLive\Controllers;

use Slim\Http\Response as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

use BLRLive\Config;
use BLRLive\Models\Team;
use Slim\Exception\{ HttpNotFoundException, HttpBadRequestException };

class TeamController {
	public static function teamUrl(Team $team) : string { // Get a team's URL
		return Config::API_BASE_URL . '/teams/' . urlencode($team->username);
	}

	public static function urlTeam(string $url) : ?Team { // Get a team based on its URL
		$route_prefix = Config::API_BASE_URL . '/teams/';
		if(!str_starts_with($url, $route_prefix)) return null;

		$username = substr($url, $route_prefix);
		return Team::get($username);
	}

	public static function teamJson(Team $team) : array { // Team representation
		return [
			'self' => TeamController::teamUrl($team),
			'username' => $team->username,
			'name' => $team->name
		];
	}

	public static function on($app) {
		$app->post('/teams', function (Request $req, Response $res, $args) {
			$data = $req->getParsedBody();
			if(!is_string($data['username']) || !is_string($data['name']))
				throw new HttpBadRequestException("Invalid fields");

			if($team = Team::get($data['username'])) // Prone to a race condition but nothing mega bad should happen
				$res = $res->withStatus(303);
			else {
				$team = Team::create($data['username'], $data['name']);
				$res = $res->withStatus(201);
			}

			return $res->withHeader('Location', teamUrl($team));
		});

		$app->get('/teams/{username}', function (Request $req, Response $res, $args) {
			$team = Team::get($args['username']) or throw new HttpNotFoundException($req, "Team not found");
			return $res->withJson(TeamController::teamJson($team));
		});

		$app->get('/teams', function (Request $req, Response $res, $args) {
			$total = Team::getTotal();

			$params = $req->getQueryParams();
			
			$limit  = intval($params['limit'] ?? 50);
			if($limit < 1 || $limit > 50)
				throw new HttpBadRequestException($req, 'Invalid limit parameter');

			$offset = intval($params['offset'] ?? 0);
			if($offset < 0)
				throw new HttpBadRequestException($req, 'Invalid offset parameter');

			$items = [];
			foreach(Team::getPaginated($offset, $limit) as $team) {
				$items[] = TeamController::teamJson($team);
			}
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
		});

		$app->put('/teams/{username}', function (Request $req, Response $res, $args) {
			$data = $req->getParsedBody();
			$team = Team::get($args['username']) or throw new HttpNotFoundException($req, "Team not found");

			if(is_string($data['name'])) $team->name = $data['name'];

			$team->save();
			return $res->withStatus(200)->withJson(TeamController::teamJson($team));
		});

		$app->delete('/teams/{username}', function (Request $req, Response $res, $args) {
			$team = Team::get($args['username']) or throw new HttpNotFoundException($req, "Team not found");
			$team->delete();
			return $res->withStatus(204);
		});
	}
}
