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
use Slim\Psr7\Response as SlimResponse;
use Util\AuthMiddleware as AuthMiddlewareAlias;
use Util\DbInit;

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

    $response->getBody()->write(json_encode(['msg' => $init->init()], JSON_THROW_ON_ERROR, 512));

    return $response;
});

$app->post('/auth/signin', static function (Request $request, Response $response) {
    $user = new User();
    try {
        $json_body = json_decode($request->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);

        $response->getBody()->write(json_encode([
            'id_token' => $user->signInUser($json_body),
        ], JSON_THROW_ON_ERROR, 512));
    } catch (RuntimeException $e) {
        $response->getBody()->write(json_encode([
            'err' => $e->getMessage(),
        ], JSON_THROW_ON_ERROR, 512));
    } catch (NestedValidationException $exception) {
        $response->getBody()->write(json_encode([
            'err' => $exception->getMessages(),
        ], JSON_THROW_ON_ERROR, 512));
    }
    return $response;
});

//TODO::/user 밑에 사용자 API Grouping 필요

$app->get('/user', static function (Request $request, Response $response) {
    $user = new User();
    $params = $request->getQueryParams();
    foreach ($params as $k => $v) {
        unset($params[$k]);
        if (strpos($k, '?')) {
            $params[substr($k, strpos($k, '?') + 1, strlen($k))] = $v;
        } else {
            $params[$k] = $v;
        }
    }

    if (count($params) === 1) {
        //TODO:: 해당 Feature는 추후 삭제(개발용으로 전체 조회 추가한 것)
        $total = $user->findAllUser();
        $response->getBody()->write(json_encode(['count' => count($total), 'list' => $total], JSON_THROW_ON_ERROR,
            512));
    } else {
        try {
            $r = $user->searchUser((object)$params);
            $response->getBody()->write(json_encode([
                'count' => count($r),
                'list' => $r,
            ], JSON_THROW_ON_ERROR, 512));
        } catch (RuntimeException $e) {
            $response->getBody()->write(json_encode([
                'err' => $e->getMessage(),
            ], JSON_THROW_ON_ERROR, 512));
        } catch (NestedValidationException $exception) {
            $response->getBody()->write(json_encode([
                'err' => $exception->getMessages(),
            ], JSON_THROW_ON_ERROR, 512));
        }
    }
    return $response;
})->add(new AuthMiddlewareAlias());

$app->post('/user', static function (Request $request, Response $response) {
    $user = new User();
    try {
        $json_body = json_decode($request->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);

        $response->getBody()->write(json_encode([
            'id' => (int)$user->addUser($json_body),
        ], JSON_THROW_ON_ERROR, 512));
    } catch (RuntimeException $e) {
        $response->getBody()->write(json_encode([
            'err' => $e->getMessage(),
        ], JSON_THROW_ON_ERROR, 512));
    } catch (NestedValidationException $exception) {
        $response->getBody()->write(json_encode([
            'err' => $exception->getMessages(),
        ], JSON_THROW_ON_ERROR, 512));
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
        ], JSON_THROW_ON_ERROR, 512));
    }

    return $response;
})->add(new AuthMiddlewareAlias());

$app->run();