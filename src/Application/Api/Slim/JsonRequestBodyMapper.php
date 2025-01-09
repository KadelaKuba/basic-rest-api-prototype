<?php

namespace App\Application\Api\Slim;

use JsonException;
use JsonMapper;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Exception\HttpBadRequestException;

class JsonRequestBodyMapper
{
    public function __construct(
        private JsonMapper $jsonMapper,
    ) {
    }

    public function mapFromRequestBodyWithJson(ServerRequestInterface $request, object $targetObject): void
    {
        try {
            $decoded = json_decode($request->getBody()->getContents(), false, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException $jsonException) {
            throw new HttpBadRequestException(
                $request,
                'Invalid request body json syntax: ' . $jsonException->getMessage(),
                $jsonException
            );
        }

        $this->jsonMapper->map($decoded, $targetObject);
    }
}
