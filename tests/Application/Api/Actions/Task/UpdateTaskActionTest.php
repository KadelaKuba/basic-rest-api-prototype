<?php

namespace Tests\Application\Api\Actions\Task;

use App\Application\Api\Actions\Task\UpdateTaskAction;
use App\Application\Api\Request\Task\UpdateTaskBody;
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
use Slim\Exception\HttpBadRequestException;
use Slim\Exception\HttpNotFoundException;
use Slim\Psr7\Factory\ResponseFactory;
use Slim\Psr7\Factory\ServerRequestFactory;
use Slim\Psr7\Response;

class UpdateTaskActionTest extends TestCase
{
    private UpdateTaskAction $updateTaskAction;

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

        $this->updateTaskAction = new UpdateTaskAction(
            $this->jsonResponderMock,
            $this->taskFacadeMock,
            $this->taskDataFactoryMock,
            $this->responseFactoryMock,
        );
    }

    public function testAction(): void
    {
        $request = (new ServerRequestFactory())->createServerRequest(HttpOptions::METHOD_PUT, '');
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

        $expectedResponseCode = HttpOptions::STATUS_OK;
        $response = new Response($expectedResponseCode);

        $this->responseFactoryMock
            ->expects($this->once())
            ->method('createResponse')
            ->with($expectedResponseCode)
            ->willReturn($response);

        $this->taskFacadeMock
            ->expects($this->once())
            ->method('edit')
            ->willReturn($task);

        $this->jsonResponderMock
            ->expects($this->once())
            ->method('respond');

        ($this->updateTaskAction)(
            $request,
            $responseMock,
            [
                UpdateTaskAction::ARGUMENT_KEY_TASK_ID => '42'
            ],
            $this->createMock(UpdateTaskBody::class),
        );
    }

    public function testMissingArgument(): void
    {
        $request = (new ServerRequestFactory())->createServerRequest(HttpOptions::METHOD_PUT, '');
        $responseMock = $this->createMock(ResponseInterface::class);

        $this->expectException(HttpBadRequestException::class);

        ($this->updateTaskAction)(
            $request,
            $responseMock,
            [],
            $this->createMock(UpdateTaskBody::class),
        );
    }

    public function testInvalidTaskId(): void
    {
        $request = (new ServerRequestFactory())->createServerRequest(HttpOptions::METHOD_PUT, '');
        $responseMock = $this->createMock(ResponseInterface::class);

        $this->taskFacadeMock
            ->expects($this->once())
            ->method('edit')
            ->willThrowException($this->createMock(HttpNotFoundException::class));

        $this->expectException(HttpNotFoundException::class);

        ($this->updateTaskAction)(
            $request,
            $responseMock,
            [
                UpdateTaskAction::ARGUMENT_KEY_TASK_ID => 'invalid id'
            ],
            $this->createMock(UpdateTaskBody::class),
        );
    }
}
