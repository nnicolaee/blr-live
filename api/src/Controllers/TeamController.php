<?php

declare(strict_types=1);

namespace BLRLive\Controllers;

use Slim\Http\Response as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Exception\{ HttpNotFoundException, HttpBadRequestException };
use BLRLive\Config;
use BLRLive\Models\Team;
use BLRLive\REST\{ Controller, HttpRoute };
use BLRLive\Schemas\{ CreateTeamRequest, UpdateTeamRequest };

#[Controller('/teams')]
class TeamController
{
    #[HttpRoute('POST')]
    public static function createTeam(Request $req, Response $res)
    {
        $body = CreateTeamRequest::from($req->getParsedBody())
            or throw new HttpBadRequestException($req);

        if ($team = Team::get($body->username)) { // Prone to a race condition but nothing mega bad should happen
            $res = $res->withStatus(303);
        } else {
            $team = Team::create(
                username: $body->username,
                name: $body->name
            );
            $res = $res->withStatus(201);
        }

        return $res->withHeader('Location', '/teams/' . urlencode($body->username));
    }

    #[HttpRoute('GET', '/{username}')]
    public static function getTeam(Request $req, Response $res, $args)
    {
        $team = Team::get($args['username'])
            or throw new HttpNotFoundException($req, "Team not found");
        return $res->withJson($team);
    }

    #[HttpRoute('GET')]
    public static function getPaginatedTeams(Request $req, Response $res)
    {
        $total = Team::getTotal();

        $params = $req->getQueryParams();

        $limit  = intval($params['limit'] ?? 50);
        if ($limit < 1 || $limit > 50) {
            throw new HttpBadRequestException($req, 'Invalid limit parameter');
        }

        $offset = intval($params['offset'] ?? 0);
        if ($offset < 0) {
            throw new HttpBadRequestException($req, 'Invalid offset parameter');
        }

        $items = Team::getPaginated($offset, $limit);
        $count = count($items);

        $prev = null;
        if ($offset > 0 && $offset - $limit < $total) { // build prev URL
            $prev_offset = max(0, $offset - $limit);
            $prev = '/teams?offset=' . $prev_offset . '&limit=' . $limit;
        }

        $next = null;
        if ($offset + $count < $total) { // build next URL
            $next_offset = $offset + $count;
            $next = '/teams?offset=' . $next_offset . '&limit=' . $limit;
        }

        $first = '/teams?offset=0&limit=' . $limit;

        $last_offset = floor($total / $limit) * $limit;
        $last = '/teams?offset=' . $last_offset . '&limit=' . $limit;

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
    public static function updateTeam(Request $req, Response $res, $args)
    {
        $body = UpdateTeamRequest::from($req->getParsedBody())
            or throw new HttpBadRequestException($req);
        $team = Team::get($args['username'])
            or throw new HttpNotFoundException($req, "Team not found");

        if ($body->name) {
            $team->name = $body->name;
        }

        $team->save();
        return $res->withStatus(200)->withJson($team);
    }

    #[HttpRoute('DELETE', '/{username}')]
    public static function deleteTeam(Request $req, Response $res, $args)
    {
        $team = Team::get($args['username'])
            or throw new HttpNotFoundException($req, "Team not found");
        $team->delete();
        return $res->withStatus(204);
    }
}
