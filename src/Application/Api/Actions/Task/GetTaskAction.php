<?php

namespace App\Application\Api\Actions\Task;

use App\Application\Api\Actions\AbstractAction;
use App\Application\Model\Task\TaskFacade;
use App\Components\Http\HttpOptions;
use App\Components\Responder\ApiResponder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Psr7\Factory\ResponseFactory;

class GetTaskAction extends AbstractAction
{
    public const ACTION_PATH = '/tasks';

    public function __construct(
        private ApiResponder $apiResponder,
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

        return $this->apiResponder->respond(
            $this->responseFactory->createResponse(HttpOptions::STATUS_OK),
            $tasks,
        );
    }
}
