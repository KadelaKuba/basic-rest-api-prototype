<?php

namespace Tests\Application\Api\Slim;

use App\Application\Api\Exception\ValidationViolationsBadRequestException;
use App\Application\Api\Slim\ActionParameterResolver;
use App\Application\Api\Slim\JsonRequestBodyMapper;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Slim\Psr7\Request;
use Slim\Psr7\Response;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ActionParameterResolverTest extends TestCase
{
    private JsonRequestBodyMapper|MockObject $bodyMapperMock;
    private ValidatorInterface|MockObject $validatorMock;
    private Request|MockObject $requestMock;
    private Response|MockObject $responseMock;

    public function setUp(): void
    {
        $this->bodyMapperMock = $this->createMock(JsonRequestBodyMapper::class);
        $this->validatorMock = $this->createMock(ValidatorInterface::class);

        $this->requestMock = $this->createMock(Request::class);
        $this->responseMock = $this->createMock(Response::class);

        $this->resolver = new ActionParameterResolver(
            $this->validatorMock,
            $this->bodyMapperMock,
        );
    }

    public function testDtoIsCreatedMappedAndValidated(): void
    {
        $action = new class() {
            public function __invoke(
                ClassTestDto $dto
            ) {
                throw new \LogicException('This method should not be executed');
            }
        };

        $this->bodyMapperMock
            ->expects($this->once())
            ->method('mapFromRequestBodyWithJson');

        $this->validatorMock
            ->expects($this->once())
            ->method('validate');

        $arguments = $this->resolver->resolveActionParameters(
            new \ReflectionMethod($action, '__invoke'),
            $this->requestMock,
            $this->responseMock,
            []
        );

        $this->assertInstanceOf(ClassTestDto::class, $arguments['dto']);
    }

    public function testObjectParameterValidationErrors(): void
    {
        $action = new class() {
            public function __invoke(
                ClassTestDto $dto
            ) {
                throw new \LogicException('This method should not be executed');
            }
        };

        $violationsList = $this->createMock(ConstraintViolationListInterface::class);
        $violationsList
            ->method('count')
            ->willReturn(1);

        $this->validatorMock
            ->expects($this->once())
            ->method('validate')
            ->willReturn($violationsList);

        $this->expectException(ValidationViolationsBadRequestException::class);

        $this->resolver->resolveActionParameters(
            new \ReflectionMethod($action, '__invoke'),
            $this->requestMock,
            $this->responseMock,
            []
        );
    }
}
