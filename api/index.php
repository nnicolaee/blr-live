<?php

declare(strict_types=1);

use Slim\Http\Response as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use Slim\Exception\HttpSpecializedException;

use BLRLive\Config;
use BLRLive\Controllers\{ TeamController, CurrentStatusController, StageController, MatchController };

require __DIR__ . '/vendor/autoload.php';

$app = AppFactory::create();
$app->setBasePath('/api');

$app->addRoutingMiddleware();

$errorMiddleware = $app->addErrorMiddleware(true, true, true);
$errorMiddleware->setDefaultErrorHandler(function (
    Request $req,
    Throwable $exception,
    bool $displayErrorDetails,
    bool $logErrors,
    bool $logErrorDetails,
    ?LoggerInterface $logger = null
) use ($app) {
    $logger?->error($exception->getMessage());

    $payload = ['error' => $exception->getMessage()];
    $code = $exception->getCode();

    $res = $app->getResponseFactory()->createResponse();
    
    if(!($exception instanceof HttpSpecializedException)) {
        $code = 500; // don't send non-HTTP error codes such as mysqli's thousands :)

        if(!$displayErrorDetails) { // censor unknown error messages if not in dev
            $payload = ['error' => 'Unexpected error. If you see this, please contact the developers :)'];
        }
    }

    if($displayErrorDetails) {
    	$payload['_message'] = $exception->getMessage();
    	$payload['_where'] = $exception->getFile() . ':' . $exception->getLine();
    	$payload['_trace'] = $exception->getTrace();
    }

    return $res->withStatus($code)->withJson($payload);
});

/*
[X] getTeams
[X] createTeam
[X] getTeam
[x] updateTeam
[X] deleteTeam
*/
TeamController::on($app);

/*
[x] getCurrentStatus
[x] updateCurrentStatus
*/
CurrentStatusController::on($app);

/*
[ ] getStages
[ ] createStage
[ ] getStage
[ ] updateStage
[ ] deleteStage
*/
StageController::on($app);

/*
[ ] getMatch
[ ] createMatch
[ ] updateMatch
[ ] deleteMatch
*/
MatchController::on($app);

$app->add(new Middlewares\TrailingSlash(false));

$app->run();
