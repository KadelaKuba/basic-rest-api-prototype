<?php

namespace App\Middlewares\Api;

use App\Components\Http\HttpOptions;
use App\Components\Responder\ApiResponder;
use Exception;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Exception\HttpException;

class ApiErrorMiddleware implements MiddlewareInterface
{
    public function __construct(
        private ApiResponder $apiResponder,
        private ResponseFactoryInterface $responseFactory,
    ) {
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        try {
            return $handler->handle($request);
        } catch (Exception $exception) {
            $code = HttpOptions::STATUS_INTERNAL_SERVER_ERROR;
            if ($exception instanceof HttpException) {
                $code = $exception->getCode();
            }

            $errorResponse = $this->responseFactory->createResponse($code);

            return $this->apiResponder->respond(
                $errorResponse,
                [],
                $exception->getMessage(),
            );
        }
    }
}
