<?php

namespace App\Application\Response;

use OpenApi\Attributes as OA;

#[OA\Schema(schema: 'Response.ErrorResponse')]
class ErrorResponse
{
    public function __construct(
        #[OA\Property]
        public string $message,
    ) {
    }
}
