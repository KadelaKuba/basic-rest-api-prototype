<?php

namespace App\Middlewares\Api;

use App\Application\Response\ErrorResponse;
use App\Components\Http\HttpOptions;
use App\Components\Responder\JsonResponder;
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
        private JsonResponder $jsonResponder,
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

            return $this->jsonResponder->respond(
                $this->responseFactory->createResponse($code),
                new ErrorResponse($exception->getMessage()),
            );
        }
    }
}
