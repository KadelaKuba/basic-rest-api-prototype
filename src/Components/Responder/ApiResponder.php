<?php

namespace App\Components\Responder;

use Psr\Http\Message\ResponseInterface as PsrResponseInterface;

class ApiResponder
{
    public function __construct(
        private JsonResponder $jsonResponder
    ) {
    }

    public function respond(
        PsrResponseInterface $response,
        mixed $result = [],
        string $errorMessage = '',
    ): PsrResponseInterface {
        return $this->jsonResponder->respond(
            $response,
            [
                'result' => [$result],
                'message' => $errorMessage,
            ],
        );
    }
}
