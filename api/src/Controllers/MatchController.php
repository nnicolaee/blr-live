<?php

declare(strict_types=1);

namespace BLRLive\Controllers;

use Slim\Http\Response as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Exception\{ HttpNotFoundException, HttpBadRequestException };
use BLRLive\REST\{ Controller, HttpRoute };
use BLRLive\Models\{ Stage, Team, MMatch, Game };

#[Controller('/matches')]
class MatchController
{
    #[HttpRoute('POST')]
    public static function createMatch(Request $req, Response $res)
    {
        $body = CreateMatchRequest::from($req->getParsedBody())
            or throw new HttpBadRequestException($req);

        if (!Stage::exists($body->stage) || !Team::exists($body->team1) || !Team::exists($body->team2)) {
            throw new HttpBadRequestException($req, 'Referenced object not found');
        }

        $match = MMatch::create(
            stage: $stage,
            team1: $team1,
            team2: $team2
        );

        return $res->withJson($match);
    }

    #[HttpRoute('GET', '/{match}')]
    public static function getMatch(Request $req, Response $res, $args)
    {
        $match = MMatch::get($args['match'])
            or throw new HttpNotFoundException($req);

        return $res->withJson($match);
    }

    #[HttpRoute('POST', '/{match}/games')]
    public static function addMatchGame(Request $req, Response $res, $args)
    {
        $match = MMatch::get($args['match'])
            or throw new HttpNotFoundException($req);

        $body = AddMatchRequest::from($req->getParsedBody())
            or throw new HttpBadRequestException($req);

        if ($body->outcome == 'team1' || $body->outcome == 'team2' || $body->outcome == 'draw') {
            $match->addGame($outcome);
            \BLRLive\Models\LiveEvents::sendEvent('gameOutcome', $outcome);
        }

        return $res->withStatus(200);
    }

    #[HttpRoute('DELETE', '/{match}/games')]
    public static function deleteMatchGame(Request $req, Response $res, $args)
    {
        $match = MMatch::get($args['match'])
            or throw new HttpNotFoundException($req);

        return $res->withStatus(204);
    }

    #[HttpRoute('PUT', '/{match}/finished')]
    public static function endMatch(Request $req, Response $res, $args)
    {
        $match = MMatch::get($args['match'])
            or throw new HttpNotFoundException($req);

        $match->status = 'finished';
        $match->save();

        return $res->withJson($match);
    }

    #[HttpRoute('DELETE', '/{match}')]
    public static function deleteMatch(Request $req, Response $res, $args)
    {
        $match = MMatch::get($args['match'])
            or throw new HttpNotFoundException($req);

        $match->delete();
        return $res->withStatus(204);
    }

    #[HttpRoute('GET', '/games/{id}')]
    public static function getGame(Request $req, Response $res, $args)
    {
        $game = Game::get($args['id'])
            or throw new HttpNotFoundException($req);
        return $res->withJson($game);
    }

    #[HttpRoute('DELETE', '/games/{id}')]
    public static function deleteGame(Request $req, Response $res, $args)
    {
        $game = Game::get($args['id'])
            or throw new HttpNotFoundException($req);
        $game->delete();
        return $res->withStatus(204);
    }
}
