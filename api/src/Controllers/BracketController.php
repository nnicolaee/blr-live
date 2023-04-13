<?php

declare(strict_types=1);

namespace BLRLive\Controllers;

use Slim\Http\Response as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use BLRLive\REST\{ Controller, HttpRoute };
use BLRLive\Models\{ Bracket };
use Slim\Exception\{ HttpNotFoundException, HttpBadRequestException };

#[Controller('/brackets')]
class BracketController
{
    #[HttpRoute('POST')]
    public static function createBracket(Request $req, Response $res)
    {
        $body = $req->getParsedBody();

        if (!isset($body['depth']) || !is_numeric($body['depth'])) {
            throw new HttpBadRequestException($req);
        }
        $depth = intval($body['depth']);

        $bracket = Bracket::createTree($depth);

        return $res->withStatus(201)->withHeader('Location', $bracket->getUrl());
    }

    #[HttpRoute('GET', '/{bracket}')]
    public static function getBracket(Request $req, Response $res, $args)
    {
        $bracket = Bracket::getBracket(intval($args['bracket']))
            or throw new HttpNotFoundException($req, 'Bracket not found');

        return $res->withJson($bracket);
    }

    #[HttpRoute('PUT', '/{bracketSlot}')]
    public static function updateBracketSlot(Request $req, Response $res, $args)
    {
        $slot = Bracket::getSlot(intval($args['bracketSlot']))
            or throw new HttpNotFoundException($req, 'Bracket slot not found');

        $body = $req->getParsedBody();
        if (isset($body['match']) && is_string($body['match'])) {
            $slot->match = MMatch::fromUrl($body['match'])?->id
                or throw new HttpNotFoundException($req, 'Referenced match not found');
        }

        $slot->save();
        return $res->withStatus(204); // FIXME return something proper
    }

    #[HttpRoute('DELETE', '/{bracketSlot}')]
    public static function deleteBracketSlot(Request $req, Response $res, $args)
    {
        $slot = Bracket::getSlot(intval($args['bracketSlot']))
            or throw new HttpNotFoundException($req, 'Bracket slot not found');
        $slot->delete();
        return $res->withStatus(204);
    }
}
