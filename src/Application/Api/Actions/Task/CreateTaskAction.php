<?php

namespace App\Application\Api\Actions\Task;

use App\Application\Api\Actions\AbstractAction;
use App\Application\Api\Request\Task\CreateTaskBody;
use App\Application\Model\Task\TaskDataFactory;
use App\Application\Model\Task\TaskFacade;
use App\Components\Http\HttpOptions;
use App\Components\Responder\ApiResponder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Psr7\Factory\ResponseFactory;

class CreateTaskAction extends AbstractAction
{
    public const ACTION_PATH = '/tasks';

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
    public function __invoke(Request $request, ResponseInterface $response, array $arguments, CreateTaskBody $createTaskBody): ResponseInterface
    {
        $taskData = $this->taskDataFactory->createFromCreateTaskBody($createTaskBody);
        $task = $this->taskFacade->create($taskData);

        return $this->apiResponder->respond(
            $this->responseFactory->createResponse(HttpOptions::STATUS_CREATED),
            $task,
        );
    }
}
