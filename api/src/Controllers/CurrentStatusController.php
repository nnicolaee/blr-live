<?php

declare(strict_types=1);

namespace BLRLive\Controllers;

use Slim\Http\Response as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Exception\{ HttpNotFoundException, HttpBadRequestException };
use BLRLive\Models\CurrentStatus;
use BLRLive\Models\LiveEvents;
use BLRLive\REST\{Controller, HttpRoute};
use BLRLive\Schemas\UpdateCurrentStatusRequest;
use BLRLive\Config;

#[Controller('/currentStatus')]
class CurrentStatusController
{
    #[HttpRoute('GET')]
    public static function getCurrentStatus(Request $req, Response $res)
    {
        return $res->withJson(CurrentStatus::get());
    }

    #[HttpRoute('PUT')]
    public static function updateCurrentStatus(Request $req, Response $res)
    {
        $currentStatus = CurrentStatus::get();
        $body = UpdateCurrentStatusRequest::from($req->getParsedBody())
            or throw new HttpBadRequestException($req);

        if ($body->stage) {
            $currentStatus->stage = $body->stage;
            LiveEvents::sendEvent('currentStatus', ['stage' => $body->stage]);
        }

        if ($body->match) {
            $currentStatus->match = $body->match;
            LiveEvents::sendEvent('currentStatus', ['match' => $body->match]);
        }

        if ($body->livestream) {
            $currentStatus->livestream = $body->livestream;
            LiveEvents::sendEvent('currentStatus', ['livestream' => $body->livestream]);
        }

        $currentStatus->save();// or throw new HttpNotFoundException($req, 'Referenced objects not found');
        return $res->withJson($currentStatus);
    }
}
