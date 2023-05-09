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

    #[HttpRoute('GET', '/{name}')]
    public static function getStage(Request $req, Response $res, $args)
    {
        $stage = Stage::get($args['name'])
            or throw new HttpNotFoundException($req, 'Stage not found');
        return $res->withJson($stage);
    }

    #[HttpRoute('PUT', '/{name}')]
    public static function updateStage(Request $req, Response $res, $args)
    {
        $body = $req->getParsedBody();
        $stage = Stage::get($args['name'])
            or throw new HttpNotFoundException($req, 'Stage not found');

        if (isset($body['bracket']) && is_string($body['bracket'])) {
            $stage->bracket = Bracket::fromUrl($body['bracket'])
                or throw new HttpNotFoundException($req, 'Referenced bracket not found');
        }

        $stage->save();
        return $res->withJson($stage);
    }

    #[HttpRoute('DELETE', '/{name}')]
    public static function deleteStage(Request $req, Response $res, $args)
    {
        $stage = Stage::get($args['name'])
            or throw new HttpNotFoundException($req, 'Stage not found');
        $stage->delete();
        return $res->withStatus(204);
    }
}
