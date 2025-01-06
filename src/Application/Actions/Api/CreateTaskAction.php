<?php

namespace App\Application\Actions\Api;

use App\Application\Actions\AbstractAction;
use App\Application\Model\Task\TaskData;
use App\Application\Model\Task\TaskFacade;
use App\Application\Model\Task\TaskStatus;
use App\Components\Responder\JsonResponder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface as Request;

class CreateTaskAction extends AbstractAction
{
    public const ACTION_PATH = '/tasks';

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
        $taskData = new TaskData('title', 'description', TaskStatus::DONE, new \DateTimeImmutable(), new \DateTimeImmutable());
        $task = $this->taskFacade->create($taskData);

        return $this->jsonResponder->respond(
            $response,
            $task,
        );
    }
}
