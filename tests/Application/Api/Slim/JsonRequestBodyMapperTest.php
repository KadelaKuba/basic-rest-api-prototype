<?php

namespace Tests\Application\Api\Slim;

use App\Application\Api\Slim\JsonRequestBodyMapper;
use JsonException;
use JsonMapper;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Exception\HttpBadRequestException;
use Slim\Psr7\Factory\StreamFactory;

class JsonRequestBodyMapperTest extends TestCase
{
    private JsonMapper|MockObject $jsonMapperMock;

    private JsonRequestBodyMapper $bodyMapper;

    public function setUp(): void
    {
        $this->jsonMapperMock = $this->createMock(JsonMapper::class);
        $this->bodyMapper = new JsonRequestBodyMapper($this->jsonMapperMock);
    }

    public function testRequestPassesItsBody(): void
    {
        $targetObject = new \stdClass();

        $this->jsonMapperMock
            ->expects($this->once())
            ->method('map');

        $body = (new StreamFactory())->createStream();
        $body->write('{}');
        $body->rewind();

        $request = $this->createMock(ServerRequestInterface::class);
        $request
            ->method('getBody')
            ->willReturn($body);

        $this->bodyMapper->mapFromRequestBodyWithJson($request, $targetObject);
    }

    public function testJsonWithSyntaxErrorThrowsBadRequest(): void
    {
        $body = (new StreamFactory())->createStream();
        $body->write('invalidSyntax');
        $body->rewind();

        $request = $this->createMock(ServerRequestInterface::class);
        $request
            ->method('getBody')
            ->willReturn($body);

        $this->expectException(HttpBadRequestException::class);

        $this->bodyMapper->mapFromRequestBodyWithJson($request, new \stdClass());
    }
}
