<?php

namespace App\Application\Api\Actions\Task;

use App\Application\Api\Actions\AbstractAction;
use App\Application\Api\Request\Task\UpdateTaskBody;
use App\Application\Model\Task\Exception\TaskNotFoundException;
use App\Application\Model\Task\TaskDataFactory;
use App\Application\Model\Task\TaskFacade;
use App\Components\Http\HttpOptions;
use App\Components\Responder\ApiResponder;
use Exception;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Exception\HttpBadRequestException;
use Slim\Exception\HttpException;
use Slim\Exception\HttpNotFoundException;
use Slim\Psr7\Factory\ResponseFactory;

class UpdateTaskAction extends AbstractAction
{
    public const ACTION_PATH = '/tasks/{id}';

    public const ARGUMENT_KEY_TASK_ID = 'id';

    public function __construct(
        private ApiResponder $apiResponder,
        private TaskFacade $taskFacade,
        private TaskDataFactory $taskDataFactory,
        private ResponseFactory $responseFactory,
    ) {
    }

    /**
     * @param array<string|int> $arguments
     */
    public function __invoke(Request $request, ResponseInterface $response, array $arguments, UpdateTaskBody $updateTaskBody): ResponseInterface
    {
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

        return $this->apiResponder->respond(
            $this->responseFactory->createResponse(HttpOptions::STATUS_OK),
            $task,
        );
    }
}
