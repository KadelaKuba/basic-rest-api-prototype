<?php

namespace Tests\Middlewares\Api;

use App\Application\Response\ErrorResponse;
use App\Components\Http\HttpOptions;
use App\Components\Responder\JsonResponder;
use App\Middlewares\Api\ApiErrorMiddleware;
use Exception;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Psr7\Factory\ResponseFactory;

class ApiErrorMiddlewareTest extends TestCase
{
    private MockObject|JsonResponder $jsonResponderMock;

    private ResponseFactory|MockObject $responseFactoryMock;

    private ApiErrorMiddleware $apiErrorMiddleware;

    private RequestHandlerInterface|MockObject $requestHandlerMock;

    private ServerRequestInterface|MockObject $requestMock;

    protected function setUp(): void
    {
        $this->jsonResponderMock = $this->createMock(JsonResponder::class);
        $this->responseFactoryMock = $this->createMock(ResponseFactory::class);

        $this->requestHandlerMock = $this->createMock(RequestHandlerInterface::class);
        $this->requestMock = $this->createMock(ServerRequestInterface::class);

        $this->apiErrorMiddleware = new ApiErrorMiddleware(
            $this->jsonResponderMock,
            $this->responseFactoryMock,
        );
    }

    public function testHandleRequestWithoutError(): void
    {
        $this->requestHandlerMock
            ->expects($this->once())
            ->method('handle');

        $this->apiErrorMiddleware->process(
            $this->requestMock,
            $this->requestHandlerMock,
        );
    }

    public function testHandleRequestWithError(): void
    {
        $responseMock = $this->createMock(ResponseInterface::class);
        $exception = new Exception('some error');
        $expectedErrorResponse = new ErrorResponse('some error');

        $this->requestHandlerMock
            ->expects($this->once())
            ->method('handle')
            ->willThrowException($exception);

        $this->responseFactoryMock
            ->expects($this->once())
            ->method('createResponse')
            ->with(HttpOptions::STATUS_INTERNAL_SERVER_ERROR)
            ->willReturn($responseMock);

        $this->jsonResponderMock
            ->expects($this->once())
            ->method('respond')
            ->with(
                $responseMock,
                $expectedErrorResponse,
            );

        $this->apiErrorMiddleware->process(
            $this->requestMock,
            $this->requestHandlerMock,
        );
    }
}
