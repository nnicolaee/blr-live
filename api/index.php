<?php
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

    $res = $app->getResponseFactory()->createResponse();
    
    if(!($exception instanceof HttpSpecializedException) && !$displayErrorDetails) { // censor unknown error messages if not in dev
    	$payload = ['error' => 'Unexpected error. If you see this, please contact the developers :)'];
    }

    if($displayErrorDetails) {
    	$payload['_message'] = $exception->getMessage();
    	$payload['_where'] = $exception->getFile() . ':' . $exception->getLine();
    	$payload['_trace'] = $exception->getTrace();
    }

    echo $exception;

    return $res->withStatus($exception->getCode())->withJson($payload);
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


// $app->get('/currentState', function(Request $req, Response $res, $args) {
// 	return $res->withJson([
// 		'stage' => Config::API_BASE_URL . '/stages/Demo 23w14',
// 		'match' => Config::API_BASE_URL . '/matches/69'
// 	]);
// });

// function teamBrief($username, $name) {
// 	return [
// 		'self' => Config::API_BASE_URL . '/teams/' . urlencode($username),
// 		'username' => $username,
// 		'name' => $name
// 	];
// }

// $app->get('/stages/Demo 23w14', function(Request $req, Response $res, $args) {
// 	return $res->withJson([
// 		'self' => Config::API_BASE_URL . '/stages/Demo 23w14',
// 		'name' => 'Demo 23w14',
// 		'year' => 2023,
// 		'type' => 'playoff',
// 		'scoreboard' => [
// 			[
// 				'place' => 1,
// 				'team' => teamBrief('test1', 'A.D.A.'),
// 				'score' => 0,
// 				'tiebreaker' => 0,
// 				'status' => 'participant'
// 			],
// 			[
// 				'place' => 2,
// 				'team' => teamBrief('test2', 'Cezara_bot'),
// 				'score' => 0,
// 				'tiebreaker' => 0,
// 				'status' => 'participant'
// 			]
// 		],
// 		'matches' => [
// 			[
// 				'self' => Config::API_BASE_URL . '/matches/69',
// 				'team1' => teamBrief('test1', 'A.D.A.'),
// 				'team2' => teamBrief('test2', 'Cezara_bot'),
// 				'score1' => 0,
// 				'score2' => 0,
// 				'status' => 'upcoming',
// 				'games' => []
// 			]
// 		]
// 	]);
// });

// $app->get('/matches/69', function(Request $req, Response $res, $args) {
// 	return $res->withJson([
// 		'self' => Config::API_BASE_URL . '/matches/69',
// 		'team1' => teamBrief('test1', 'A.D.A.'),
// 		'team2' => teamBrief('test2', 'Cezara_bot'),
// 		'score1' => 0,
// 		'score2' => 0,
// 		'status' => 'upcoming',
// 		'games' => []
// 	]);
// });
$app->add(new Middlewares\TrailingSlash(false));

$app->run();
