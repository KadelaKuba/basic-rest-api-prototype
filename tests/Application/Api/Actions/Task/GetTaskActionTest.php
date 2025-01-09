<?php

namespace Tests\Application\Api\Actions\Task;

use App\Application\Api\Actions\Task\GetTaskAction;
use App\Application\Model\Task\TaskFacade;
use App\Components\Http\HttpOptions;
use App\Components\Responder\JsonResponder;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Slim\Psr7\Factory\ResponseFactory;
use Slim\Psr7\Factory\ServerRequestFactory;
use Slim\Psr7\Response;

class GetTaskActionTest extends TestCase
{
    private GetTaskAction $getTaskAction;

    private MockObject|JsonResponder $jsonResponderMock;

    private MockObject|TaskFacade $taskFacadeMock;

    private MockObject|ResponseFactory $responseFactoryMock;

    public function setUp(): void
    {
        parent::setUp();

        $this->jsonResponderMock = $this->createMock(JsonResponder::class);
        $this->taskFacadeMock = $this->createMock(TaskFacade::class);
        $this->responseFactoryMock = $this->createMock(ResponseFactory::class);

        $this->getTaskAction = new GetTaskAction(
            $this->jsonResponderMock,
            $this->taskFacadeMock,
            $this->responseFactoryMock,
        );
    }

    public function testAction(): void
    {
        $request = (new ServerRequestFactory())->createServerRequest(HttpOptions::METHOD_GET, '');
        $responseMock = $this->createMock(ResponseInterface::class);

        $expectedResponseCode = HttpOptions::STATUS_OK;
        $response = new Response($expectedResponseCode);

        $this->responseFactoryMock
            ->expects($this->once())
            ->method('createResponse')
            ->with($expectedResponseCode)
            ->willReturn($response);

        $tasks = [];
        $this->taskFacadeMock
            ->expects($this->once())
            ->method('getAllTasks')
            ->willReturn($tasks);

        $this->jsonResponderMock
            ->expects($this->once())
            ->method('respond')
            ->with($response, $tasks);

        ($this->getTaskAction)(
            $request,
            $responseMock,
            []
        );
    }
}
