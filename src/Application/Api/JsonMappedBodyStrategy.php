<?php

namespace App\Application\Api;

use App\Application\Api\Exception\ValidationViolationsBadRequestException;
use JsonException;
use JsonMapper;
use LogicException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use ReflectionClass;
use ReflectionMethod;
use ReflectionNamedType;
use Slim\Exception\HttpBadRequestException;
use Slim\Interfaces\InvocationStrategyInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class JsonMappedBodyStrategy implements InvocationStrategyInterface
{
    public function __construct(
        private ValidatorInterface $validator,
        private JsonMapper $jsonMapper,
    ) {
    }

    public function __invoke(callable $callable, ServerRequestInterface $request, ResponseInterface $response, array $routeArguments): ResponseInterface
    {
        $actionReflection = new ReflectionMethod($callable[0], '__invoke');

        $actionArguments = $this->resolveActionParameters(
            $actionReflection,
            $request,
            $response,
            $routeArguments
        );

        return $callable(...$actionArguments);
    }

    /**
     * @param array|string[] $inPathRouteArguments
     * @return array<mixed>
     */
    public function resolveActionParameters(
        ReflectionMethod $actionReflection,
        ServerRequestInterface $request,
        ResponseInterface $response,
        array $inPathRouteArguments,
    ): array {
        $actionArguments = [];

        foreach ($actionReflection->getParameters() as $parameter) {
            $parameterType = $parameter->getType();
            $parameterName = $parameter->getName();

            if ($parameterType === null) {
                throw new LogicException('Action must have defined type for invoke parameters');
            }

            if ($parameterType->isBuiltin()) {
                $actionArguments[$parameterName] = $inPathRouteArguments;

                continue;
            }

            $actionArguments[$parameterName] = $this->resolveObjectParameter($parameterType, $request, $response);
        }

        return $actionArguments;
    }

    private function resolveObjectParameter(
        ReflectionNamedType $parameterType,
        ServerRequestInterface $request,
        ResponseInterface $response,
    ): object {
        $parameterClass = $parameterType->getName();
        /** @phpstan-var class-string $parameterClass */
        $parameterTypeReflection = new ReflectionClass($parameterClass);

        if ($parameterTypeReflection->implementsInterface(ServerRequestInterface::class)) {
            return $request;
        }

        if ($parameterTypeReflection->implementsInterface(ResponseInterface::class)) {
            return $response;
        }

        $object = $parameterTypeReflection->newInstance();

        try {
            $decoded = json_decode($request->getBody()->getContents(), false, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException $jsonException) {
            throw new HttpBadRequestException(
                $request,
                'Invalid request body json syntax: ' . $jsonException->getMessage(),
                $jsonException
            );
        }

        $this->jsonMapper->map($decoded, $object);

        $errors = $this->validator->validate($object);
        if ($errors->count() > 0) {
            throw ValidationViolationsBadRequestException::create($request, $errors);
        }

        return $object;
    }
}
