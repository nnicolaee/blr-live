<?php

declare(strict_types=1);

namespace BLRLive\Controllers;

use Slim\Http\Response as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Exception\{ HttpNotFoundException, HttpBadRequestException };
use BLRLive\REST\{ Controller, HttpRoute };
use BLRLive\Models\{ Bracket, Stage, MMatch, LiveEvents };
use BLRLive\Schemas\{ CreateBracketRequest, UpdateBracketSlotRequest };

#[Controller('/brackets')]
class BracketController
{
    #[HttpRoute('POST')]
    public static function createBracket(Request $req, Response $res)
    {
        $body = CreateBracketRequest::from($req->getParsedBody())
            or throw new HttpBadRequestException($req);
        $bracket = Bracket::createTree($body->depth);

        return $res->withStatus(201)->withHeader('Location', '/brackets/' . $bracket->id)->withJson($bracket);
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

        $body = UpdateBracketSlotRequest::from($req->getParsedBody())
            or throw new HttpBadRequestException($req);
        

        //if(isset($body->match)) {
            /*if (!is_null($body->match) && !MMatch::exists(''.$body->match)) {
                throw new HttpBadRequestException($req, 'Referenced match not found');
            }*/
            $slot->match = $body->match;
        //}

        LiveEvents::sendEvent('bracket', ['stage' => $slot->getStage()]);

        $slot->save();
        return $res->withStatus(200);
    }

    #[HttpRoute('DELETE', '/{bracketSlot}')]
    public static function deleteBracketSlot(Request $req, Response $res, $args)
    {
        $slot = Bracket::getSlot(intval($args['bracketSlot']))
            or throw new HttpNotFoundException($req, 'Bracket slot not found');

        LiveEvents::sendEvent('bracket', ['stage' => $slot->getStage()]);
        
        $slot->delete();
        return $res->withStatus(204);
    }
}
