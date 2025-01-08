<?php

namespace App\Application\Api\Actions\Task;

use App\Application\Api\Actions\AbstractAction;
use App\Application\Model\Task\TaskFacade;
use App\Components\Http\HttpOptions;
use App\Components\Responder\JsonResponder;
use OpenApi\Attributes as OA;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Psr7\Factory\ResponseFactory;

#[OA\Get(
    path: self::ACTION_PATH,
    responses: [
        new OA\Response(
            response: HttpOptions::STATUS_OK,
            description: 'HTTP 200 OK',
            content: [new OA\JsonContent(ref: '#/components/schemas/Response.ArrayTaskResponse')],
        ),
        new OA\Response(
            response: HttpOptions::STATUS_INTERNAL_SERVER_ERROR,
            description: 'HTTP 500 Internal Server Error',
            content: new OA\JsonContent(ref: '#/components/schemas/Response.ErrorResponse')
        ),
    ]
)]
class GetTaskAction extends AbstractAction
{
    public const ACTION_PATH = '/tasks';

    public function __construct(
        private JsonResponder $jsonResponder,
        private TaskFacade $taskFacade,
        private ResponseFactory $responseFactory,
    ) {
    }

    /**
     * @param array<string|int> $arguments
     */
    public function __invoke(Request $request, ResponseInterface $response, array $arguments): ResponseInterface
    {
        $tasks = $this->taskFacade->getAllTasks();

        return $this->jsonResponder->respond(
            $this->responseFactory->createResponse(HttpOptions::STATUS_OK),
            $tasks,
        );
    }
}
