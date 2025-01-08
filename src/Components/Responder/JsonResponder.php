<?php

declare(strict_types=1);

namespace App\Components\Responder;

use App\Components\Http\HttpOptions;
use Psr\Http\Message\ResponseInterface;

class JsonResponder
{
    public function respond(
        ResponseInterface $response,
        mixed $data,
    ): ResponseInterface {
        $payload = json_encode($data, JSON_THROW_ON_ERROR);
        $response->getBody()->write($payload);

        return $response
            ->withHeader(HttpOptions::HEADER_CONTENT_TYPE, HttpOptions::HEADER_CONTENT_TYPE_JSON);
    }
}
