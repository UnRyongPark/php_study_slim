<?php

namespace Util;

use Firebase\JWT\JWT;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;
use UnexpectedValueException;


class AuthMiddleware
{
    /**
     * @param Request $request
     * @param RequestHandler $handler
     * @return Response
     */
    public function __invoke(Request $request, RequestHandler $handler): Response
    {
        $token = $request->getHeader('id_token');

        if ($token === []) {
            return $this->returnErr('Need Id_token');
        }

        try {
            $tokenDecode = JWT::decode($token[0], SAMPLE_JWT_KEY, ['HS256']);
            $request->withAttribute('idToken', $tokenDecode);

            $response = $handler->handle($request);
            $existingContent = (string)$response->getBody();

            $response = new Response();
            $response->getBody()->write($existingContent);

            return $response;
        } catch (UnexpectedValueException $e) {
            return $this->returnErr($e->getMessage());
        }
    }

    /**
     * @param $msg
     * @return Response
     */
    private function returnErr($msg): Response
    {
        $response = new Response();
        $response->getBody()->write(json_encode(['err' => $msg]));
        return $response;
    }
}