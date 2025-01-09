<?php

namespace Tests\Components\Responder;

use App\Components\Http\HttpOptions;
use App\Components\Responder\JsonResponder;
use PHPUnit\Framework\TestCase;
use Slim\Psr7\Response;

class JsonResponderTest extends TestCase
{
    public function testRespond(): void
    {
        $jsonResponder = new JsonResponder();
        $expectedHeaders = [
            HttpOptions::HEADER_CONTENT_TYPE => [
                HttpOptions::HEADER_CONTENT_TYPE_JSON,
            ],
        ];
        $expectedContent = ['content'];

        $response = $jsonResponder->respond(
            new Response(),
            $expectedContent,
        );

        self::assertSame($expectedHeaders, $response->getHeaders());

        $response->getBody()->rewind();
        self::assertSame(json_encode($expectedContent, JSON_THROW_ON_ERROR), $response->getBody()->getContents());
    }
}
