<?php

declare(strict_types=1);

namespace App\Application\Actions;

use App\Components\Http\HttpOptions;
use App\Components\Responder\JsonResponder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Psr7\Factory\ResponseFactory;

class DefaultAction extends AbstractAction
{
    public const ACTION_PATH = '/{path:.*}';
    public function __construct(
        private JsonResponder $jsonResponder,
        private readonly ResponseFactory $responseFactory,
    ) {
    }

    /**
     * @param Request $request
     * @param ResponseInterface $response
     * @param array<string|int> $arguments
     */
    public function __invoke(Request $request, ResponseInterface $response, array $arguments): ResponseInterface
    {
        return $this->jsonResponder->respond(
            $this->responseFactory->createResponse(HttpOptions::STATUS_OK),
            "test",
        );
    }
}
