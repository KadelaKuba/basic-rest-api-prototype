<?php

namespace App\Application\Api\Actions\Task;

use App\Application\Api\Actions\AbstractAction;
use App\Application\Api\Request\Task\UpdateTaskBody;
use App\Application\Model\Task\Exception\TaskNotFoundException;
use App\Application\Model\Task\TaskDataFactory;
use App\Application\Model\Task\TaskFacade;
use App\Components\Http\HttpOptions;
use App\Components\Responder\JsonResponder;
use Exception;
use OpenApi\Attributes as OA;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Exception\HttpBadRequestException;
use Slim\Exception\HttpNotFoundException;
use Slim\Psr7\Factory\ResponseFactory;

#[OA\Put(
    path: self::ACTION_PATH,
    requestBody: new OA\RequestBody(
        required: true,
        content: new OA\JsonContent(ref: '#/components/schemas/Request.Task.UpdateTaskBody')
    ),
    parameters: [
        new OA\Parameter(
            name: 'id',
            description: 'ID of task',
            in: "path",
            required: true,
            schema: new OA\Schema(type: 'integer', minimum: 1)
        ),
    ],
    responses: [
        new OA\Response(
            response: HttpOptions::STATUS_OK,
            description: 'HTTP 201 OK',
            content: new OA\JsonContent(ref: '#/components/schemas/Response.TaskResponse'),
        ),
        new OA\Response(
            response: HttpOptions::STATUS_INTERNAL_SERVER_ERROR,
            description: 'HTTP 500 Internal Server Error',
            content: new OA\JsonContent(ref: '#/components/schemas/Response.ErrorResponse')
        ),
    ]
)]
class UpdateTaskAction extends AbstractAction
{
    public const ACTION_PATH = '/tasks/{id}';

    public const ARGUMENT_KEY_TASK_ID = 'id';

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
        UpdateTaskBody $updateTaskBody
    ): ResponseInterface {
        if (array_key_exists(self::ARGUMENT_KEY_TASK_ID, $arguments) === false) {
            throw new HttpBadRequestException($request, 'Argument task id missing.');
        }

        $taskId = (int)$arguments[self::ARGUMENT_KEY_TASK_ID];

        $taskData = $this->taskDataFactory->createFromUpdateTaskBody($updateTaskBody);

        try {
            $task = $this->taskFacade->edit($taskId, $taskData);
        } catch (Exception $exception) {
            if ($exception instanceof TaskNotFoundException) {
                throw new HttpNotFoundException($request, $exception->getMessage());
            }

            throw $exception;
        }

        return $this->jsonResponder->respond(
            $this->responseFactory->createResponse(HttpOptions::STATUS_OK),
            $task,
        );
    }
}
