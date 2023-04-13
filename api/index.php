<?php

declare(strict_types=1);

use Slim\Http\Response as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Routing\RouteCollectorProxy;
use Slim\Factory\AppFactory;
use Slim\Exception\HttpSpecializedException;

use BLRLive\Config;
use BLRLive\Controllers\{ TeamController, CurrentStatusController, StageController, MatchController, SSEController };

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

$app->add(new Middlewares\TrailingSlash(false));

foreach([
    CurrentStatusController::class,
    MatchController::class,
    SSEController::class,
    StageController::class,
    TeamController::class
] as $controllerClass)
{
    $classReflection = new ReflectionClass($controllerClass);
    //DBG: var_dump($classReflection);

    [$route] = $classReflection->getAttributes('BLRLive\REST\Controller')[0]->getArguments() + [''];

    // Register controllers' routes based on their attributes
    $app->group($route, function (RouteCollectorProxy $group) use ($classReflection, $route) {
        foreach($classReflection->getMethods(ReflectionMethod::IS_STATIC) as $methodReflection)
        {
            $attrs = $methodReflection->getAttributes('BLRLive\REST\HttpRoute');
            if(!$attrs) continue;
            if([$method, $pattern] = $attrs[0]->getArguments() + ['GET', ''])
            {
                //DBG: echo "REGISTERED: $method $route$pattern -> " . $classReflection->getName() . "::" . $methodReflection->name . "\n";
                $group->map([$method], $pattern ?? '', [$classReflection->getName(), $methodReflection->name]);
            }
        }
    });
}

$app->run();
