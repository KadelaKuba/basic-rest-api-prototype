<?php

namespace App\Application\Actions\Api;

use App\Application\Actions\AbstractAction;
use App\Application\Model\Task\TaskFacade;
use App\Components\Responder\JsonResponder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface as Request;

class GetTaskAction extends AbstractAction
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
        $tasks = $this->taskFacade->getAllTasks();

        return $this->jsonResponder->respond(
            $response,
            $tasks,
        );
    }
}
