<?php

require_once '../Application/User/User.php';
require_once '../Util/DbInit.php';
require_once '../Util/AuthMiddleware.php';

use Application\User;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface;
use Respect\Validation\Exceptions\NestedValidationException;
use Slim\Factory\AppFactory;
use Util\AuthMiddleware as AuthMiddlewareAlias;
use Util\DbInit;
use Slim\Psr7\Response as SlimResponse;

require __DIR__ . '/../vendor/autoload.php';

const SAMPLE_JWT_KEY = 'example_key';

$app = AppFactory::create();

$app->addErrorMiddleware(true, true, true);

$app->add(static function (Request $request, RequestHandlerInterface $handler) {
    $response = $handler->handle($request);
    $existingContent = (string)$response->getBody();

    $response = new SlimResponse();
    $response = $response->withHeader('Content-type', 'application/json');

    $response->getBody()->write($existingContent);

    return $response;
});

$app->get('/', static function (Request $request, Response $response) {
    $init = new DbInit();

    $response->getBody()->write(json_encode(['msg' => $init->init()]));

    return $response;
});

$app->post('/auth/signin', static function (Request $request, Response $response) {
    $user = new User();
    try {
        $json_body = json_decode($request->getBody()->getContents(), true);

        $response->getBody()->write(json_encode([
            'id_token' => $user->signInUser($json_body),
        ]));
    } catch (RuntimeException $e) {
        $response->getBody()->write(json_encode([
            'err' => $e->getMessage(),
        ]));
    } catch (NestedValidationException $exception) {
        $response->getBody()->write(json_encode([
            'err' => $exception->getMessages(),
        ]));
    }
    return $response;
});

$app->get('/user', static function (Request $request, Response $response) {
    $user = new User();
    $response->getBody()->write(json_encode($user->findAllUser()));
    return $response;
})->add(new AuthMiddlewareAlias());

$app->post('/user', static function (Request $request, Response $response) {
    $user = new User();
    try {
        $json_body = json_decode($request->getBody()->getContents(), true);

        $response->getBody()->write(json_encode([
            'id' => (int)$user->addUser($json_body),
        ]));
    } catch (RuntimeException $e) {
        $response->getBody()->write(json_encode([
            'err' => $e->getMessage(),
        ]));
    } catch (NestedValidationException $exception) {
        $response->getBody()->write(json_encode([
            'err' => $exception->getMessages(),
        ]));
    }
    return $response;
});

$app->get('/user/{id}', static function (Request $request, Response $response, $args) {
    $user = new User();
    try {
        $response->getBody()->write((string)$user->getUser($args['id']));
    } catch (RuntimeException $e) {
        $response->getBody()->write(json_encode([
            'err' => $e->getMessage(),
        ]));
    }

    return $response;
})->add(new AuthMiddlewareAlias());

$app->run();