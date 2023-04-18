<?php

declare(strict_types=1);

use Slim\Psr7\Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Routing\RouteCollectorProxy;
use Slim\Factory\AppFactory;
use Slim\Exception\HttpSpecializedException;
use BLRLive\Config;
use BLRLive\Controllers\{
    TeamController,
    CurrentStatusController,
    StageController,
    MatchController,
    SSEController,
    BracketController
};
use BLRLive\Models\AuthModel;

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

    if (!($exception instanceof HttpSpecializedException)) {
        $code = 500; // don't send non-HTTP error codes such as mysqli's thousands :)

        if (!$displayErrorDetails) { // censor unknown error messages if not in dev
            $payload = ['error' => 'Unexpected error. If you see this, please contact the developers :)'];
        }
    }

    if ($displayErrorDetails) {
        $payload['_message'] = $exception->getMessage();
        $payload['_where'] = $exception->getFile() . ':' . $exception->getLine();
        $payload['_trace'] = $exception->getTrace();
    }

    return $res->withStatus($code)->withJson($payload);
});

$app->add(new Middlewares\TrailingSlash(false));

// HTTP Basic authentication. Why am I parsing this by hand? Only God knows...
$authenticateBefore = function (Request $req, RequestHandler $handler) {
    $resUnauth = (new Response())->withStatus(401)->withHeader('WWW-Authenticate', 'Basic realm="BLR Live"');
    if (!($auths = $req->getHeader('Authorization'))) {
        return $resUnauth;
    }

    if (!str_starts_with($auths[0], 'Basic ')) {
        return $resUnauth;
    }

    [$user, $pass] = explode(':', base64_decode(substr($auths[0], 6)));
    if (AuthModel::authenticate($user, $pass)) {
        return $handler->handle($req);
    } else {
        return $resUnauth;
    }
};

$controllers = [
    BracketController::class,
    CurrentStatusController::class,
    MatchController::class,
    SSEController::class,
    StageController::class,
    TeamController::class
];

foreach ($controllers as $controllerClass) {
    $classReflection = new ReflectionClass($controllerClass);

    [$route] = $classReflection->getAttributes('BLRLive\REST\Controller')[0]->getArguments() + [''];

    // Register controllers' routes based on their attributes
    $app->group(
        $route,
        function (RouteCollectorProxy $group) use ($classReflection, $authenticateBefore) {
            foreach ($classReflection->getMethods(ReflectionMethod::IS_STATIC) as $methodReflection) {
                $attrs = $methodReflection->getAttributes('BLRLive\REST\HttpRoute');
                if (!$attrs) {
                    continue;
                }
                if ([$method, $pattern] = $attrs[0]->getArguments() + ['GET', '']) {
                    $r = $group->map([$method], $pattern ?? '', [$classReflection->getName(), $methodReflection->name]);

                    // Authenticate *all* non-GET routes
                    // Otherwise, GET routes should be accessible to all
                    if (strcasecmp($method, 'GET')) {
                        $r->add($authenticateBefore);
                    }
                }
            }
        }
    );
}

$app->run();
