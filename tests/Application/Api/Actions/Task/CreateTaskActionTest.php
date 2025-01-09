<?php

namespace Tests\Application\Api\Actions\Task;

use App\Application\Api\Actions\Task\CreateTaskAction;
use App\Application\Api\Request\Task\CreateTaskBody;
use App\Application\Model\Task\Task;
use App\Application\Model\Task\TaskData;
use App\Application\Model\Task\TaskDataFactory;
use App\Application\Model\Task\TaskFacade;
use App\Application\Model\Task\TaskStatus;
use App\Components\Http\HttpOptions;
use App\Components\Responder\JsonResponder;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Slim\Psr7\Factory\ResponseFactory;
use Slim\Psr7\Factory\ServerRequestFactory;
use Slim\Psr7\Response;

class CreateTaskActionTest extends TestCase
{
    private CreateTaskAction $createTaskAction;

    private MockObject|JsonResponder $jsonResponderMock;

    private MockObject|TaskFacade $taskFacadeMock;

    private MockObject|ResponseFactory $responseFactoryMock;

    private TaskDataFactory|MockObject $taskDataFactoryMock;


    public function setUp(): void
    {
        parent::setUp();

        $this->jsonResponderMock = $this->createMock(JsonResponder::class);
        $this->taskFacadeMock = $this->createMock(TaskFacade::class);
        $this->responseFactoryMock = $this->createMock(ResponseFactory::class);
        $this->taskDataFactoryMock = $this->createMock(TaskDataFactory::class);

        $this->createTaskAction = new CreateTaskAction(
            $this->jsonResponderMock,
            $this->taskFacadeMock,
            $this->taskDataFactoryMock,
            $this->responseFactoryMock,
        );
    }

    public function testAction(): void
    {
        $request = (new ServerRequestFactory())->createServerRequest(HttpOptions::METHOD_POST, '');
        $responseMock = $this->createMock(ResponseInterface::class);

        $taskData = TaskData::create(
            'title',
            '',
            TaskStatus::IN_PROGRESS,
            new \DateTimeImmutable(),
            new \DateTimeImmutable(),
        );
        $task = Task::create($taskData);
        $task->setId(42);

        $expectedResponseCode = HttpOptions::STATUS_CREATED;
        $response = new Response($expectedResponseCode);

        $this->responseFactoryMock
            ->expects($this->once())
            ->method('createResponse')
            ->with($expectedResponseCode)
            ->willReturn($response);

        $this->taskFacadeMock
            ->expects($this->once())
            ->method('create')
            ->willReturn($task);

        $this->jsonResponderMock
            ->expects($this->once())
            ->method('respond');

        ($this->createTaskAction)(
            $request,
            $responseMock,
            [],
            $this->createMock(CreateTaskBody::class),
        );
    }
}
