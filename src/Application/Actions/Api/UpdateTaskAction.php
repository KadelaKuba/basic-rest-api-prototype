<?php

namespace App\Application\Actions\Api;

use App\Application\Actions\AbstractAction;
use App\Application\Model\Task\TaskData;
use App\Application\Model\Task\TaskFacade;
use App\Application\Model\Task\TaskStatus;
use App\Components\Responder\JsonResponder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Exception\HttpException;

class UpdateTaskAction extends AbstractAction
{
    public const ACTION_PATH = '/tasks/{id}';

    public const ARGUMENT_KEY_TASK_ID = 'id';

    public function __construct(
        private JsonResponder $jsonResponder,
        private TaskFacade $taskFacade
    ) {
    }

    /**
     * @param array<string|int> $arguments
     */
    public function __invoke(Request $request, ResponseInterface $response, array $arguments): ResponseInterface
    {
        if (array_key_exists(self::ARGUMENT_KEY_TASK_ID, $arguments) === false) {
            throw new HttpException($request, 'Argument task id missing.');
        }

        $taskId = (int)$arguments[self::ARGUMENT_KEY_TASK_ID];

        $taskData = new TaskData('title2', 'description2', TaskStatus::DONE, new \DateTimeImmutable(), new \DateTimeImmutable());
        $task = $this->taskFacade->edit($taskId, $taskData);

        return $this->jsonResponder->respond(
            $response,
            $task,
        );
    }
}
