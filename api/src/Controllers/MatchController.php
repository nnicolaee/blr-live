<?php

declare(strict_types=1);

namespace BLRLive\Controllers;

use Slim\Http\Response as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Exception\{ HttpNotFoundException, HttpBadRequestException };
use BLRLive\REST\{ Controller, HttpRoute };
use BLRLive\Models\{ Stage, Team, MMatch, Game, LiveEvents };
use BLRLive\Schemas\{ CreateMatchRequest, AddGameRequest };

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
            stage: $body->stage,
            team1: $body->team1,
            team2: $body->team2
        );

        LiveEvents::sendEvent('match', $match->jsonSerialize());

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

        $body = AddGameRequest::from($req->getParsedBody())
            or throw new HttpBadRequestException($req);

        if ($body->outcome == 'team1' || $body->outcome == 'team2' || $body->outcome == 'draw') {
            $game = $match->addGame($body->outcome);
            LiveEvents::sendEvent('game', $game->jsonSerialize());
        } else {
            throw new HttpBadRequestException($req);
        }

        LiveEvents::sendEvent('match', $match->jsonSerialize());
        LiveEvents::sendEvent('scoreboard', $match->jsonSerialize());

        return $res->withStatus(200)->withJson($game);
    }

    #[HttpRoute('PUT', '/{match}/finished')]
    public static function endMatch(Request $req, Response $res, $args)
    {
        $match = MMatch::get($args['match'])
            or throw new HttpNotFoundException($req);

        $m = $match->jsonSerialize();

        if($m->score1 > $m->score2)
            $match->status = 'win1';
        else if($m->score1 < $m->score2)
            $match->status = 'win2';
        else
            $match->status = 'draw';
        $match->save();

        LiveEvents::sendEvent('match', $match->jsonSerialize());
        LiveEvents::sendEvent('scoreboard', $match->jsonSerialize());

        return $res->withJson($match);
    }


    #[HttpRoute('DELETE', '/{match}/finished')]
    public static function unendMatch(Request $req, Response $res, $args)
    {
        $match = MMatch::get($args['match'])
            or throw new HttpNotFoundException($req);

        $m = $match->jsonSerialize();

        $match->status = 'upcoming';
        $match->save();

        LiveEvents::sendEvent('match', $match->jsonSerialize());
        LiveEvents::sendEvent('scoreboard', $match->jsonSerialize());

        return $res->withJson($match);
    }

    #[HttpRoute('DELETE', '/{match}')]
    public static function deleteMatch(Request $req, Response $res, $args)
    {
        $match = MMatch::get($args['match'])
            or throw new HttpNotFoundException($req);
        LiveEvents::sendEvent('match', ['stage' => $match->stage]);
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
        LiveEvents::sendEvent('match', null);
        LiveEvents::sendEvent('scoreboard', null);
        $game->delete();
        
        return $res->withStatus(204);
    }
}
