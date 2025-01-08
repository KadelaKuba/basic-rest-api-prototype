<?php

namespace App\Application\Api\Actions\Task;

use App\Application\Api\Actions\AbstractAction;
use App\Application\Api\Request\Task\CreateTaskBody;
use App\Application\Model\Task\TaskDataFactory;
use App\Application\Model\Task\TaskFacade;
use App\Application\Response\Task\TaskResponse;
use App\Components\Http\HttpOptions;
use App\Components\Responder\JsonResponder;
use OpenApi\Attributes as OA;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Psr7\Factory\ResponseFactory;

#[OA\Post(
    path: self::ACTION_PATH,
    requestBody: new OA\RequestBody(
        required: true,
        content: new OA\JsonContent(ref: '#/components/schemas/Request.Task.CreateTaskBody')
    ),
    responses: [
        new OA\Response(
            response: HttpOptions::STATUS_CREATED,
            description: 'HTTP 201 Created',
            content: new OA\JsonContent(ref: '#/components/schemas/Response.TaskResponse'),
        ),
        new OA\Response(
            response: HttpOptions::STATUS_BAD_REQUEST,
            description: 'HTTP 400 Bad Request',
            content: new OA\JsonContent(ref: '#/components/schemas/Response.ErrorResponse')
        ),
        new OA\Response(
            response: HttpOptions::STATUS_INTERNAL_SERVER_ERROR,
            description: 'HTTP 500 Internal Server Error',
            content: new OA\JsonContent(ref: '#/components/schemas/Response.ErrorResponse')
        ),
    ]
)]
class CreateTaskAction extends AbstractAction
{
    public const ACTION_PATH = '/tasks';

    public function __construct(
        private JsonResponder $jsonResponder,
        private TaskFacade $taskFacade,
        private TaskDataFactory $taskDataFactory,
        private ResponseFactory $responseFactory,
    ) {
    }

    /**
     * @param array<string|int> $arguments
     */
    public function __invoke(
        Request $request,
        ResponseInterface $response,
        array $arguments,
        CreateTaskBody $createTaskBody
    ): ResponseInterface {
        $taskData = $this->taskDataFactory->createFromCreateTaskBody($createTaskBody);
        $task = $this->taskFacade->create($taskData);

        return $this->jsonResponder->respond(
            $this->responseFactory->createResponse(HttpOptions::STATUS_CREATED),
            TaskResponse::fromTask($task),
        );
    }
}
