<?php

declare(strict_types=1);

namespace BLRLive\Controllers;

use Slim\Http\Response as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Exception\{ HttpNotFoundException, HttpBadRequestException };
use BLRLive\Models\Stage;
use BLRLive\REST\{ Controller, HttpRoute };

#[Controller('/stages')]
class StageController
{
    #[HttpRoute('POST')]
    public static function createStage(Request $req, Response $res)
    {
        $body = CreateStageRequest::from($req->getParsedBody())
            or throw new HttpBadRequestException($req);

        if ($stage = Stage::get($body->name)) {
            $res = $res->withStatus(303);
        } else {
            $stage = Stage::create(
                name: $body->name
            );
            $res = $res->withStatus(201);
        }

        return $res->withHeader('Location', '/stages/' . urlencode($stage->name));
    }

    #[HttpRoute('GET')]
    public static function getStages(Request $req, Response $res)
    {
        // IMPORTANT: I intentionally chose not to paginate because the number
        // of stages should be very small (1-3 stages per year). If we ever get
        // to >50 stages, consider paginating.
        return $res->withJson([
            'stages' => Stage::getAll(brief: true)
        ]);
    }

    #[HttpRoute('GET', '/{stage}')]
    public static function getStage(Request $req, Response $res, $args)
    {
        $stage = Stage::get($args['stage'])
            or throw new HttpNotFoundException($req, 'Stage not found');
        return $res->withJson($stage);
    }

    #[HttpRoute('PUT', '/{stage}')]
    public static function updateStage(Request $req, Response $res, $args)
    {
        $body = UpdateStageRequest::from($req->getParsedBody())
            or throw new HttpBadRequestException($req);
        $stage = Stage::get($args['stage'])
            or throw new HttpNotFoundException($req, 'Stage not found');

        if ($body->bracket) {
            $stage->bracket = $body->bracket;
        }

        $stage->save();
        return $res->withJson($stage);
    }

    #[HttpRoute('DELETE', '/{stage}')]
    public static function deleteStage(Request $req, Response $res, $args)
    {
        $stage = Stage::get($args['stage'])
            or throw new HttpNotFoundException($req, 'Stage not found');
        $stage->delete();
        return $res->withStatus(204);
    }

    #[HttpRoute('PUT', '/{stage}/teams/{team}')]
    public static function addParticipation(Request $req, Response $res, $args)
    {
        $stage = Stage::get($args['stage'])
            or throw new HttpNotFoundException($req, 'Stage not found');
        $team = Team::get($args['team'])
            or throw new HttpNotFoundException($req, 'Team not found');

        return $res->withJson(Participation::create(
            team: $team->username,
            stage: $stage->name
        ));
    }

    #[HttpRoute('PATCH', '/{stage}/teams/{team}')]
    public static function editParticipation(Request $req, Response $res, $args)
    {
        $body = UpdateParticipationRequest::from($req->getParsedBody())
            or throw new HttpBadRequestException($req);
        $par = Participation::get($args['stage'], $args['team'])
            or throw new HttpNotFoundException($req, 'Team does not participate in stage');

        if(isset($body->status)) $par->status = $body->status;

        $par->save();
        return $res->withJson($par);
    }

    #[HttpRoute('DELETE', '/{stage}/teams/{team}')]
    public static function deleteParticipation(Request $req, Response $res, $args)
    {
        $par = Participation::get($args['stage'], $args['team'])
            or throw new HttpNotFoundException($req, 'Team does not participate in stage');
        $par->delete();
        return $res->withStatus(204);
    }
}
