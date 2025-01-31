<?php

namespace App\Application\Api\Slim;

use App\Application\Api\Exception\ValidationViolationsBadRequestException;
use LogicException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use ReflectionClass;
use ReflectionMethod;
use ReflectionNamedType;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ActionParameterResolver
{
    public function __construct(
        private ValidatorInterface $validator,
        private JsonRequestBodyMapper $jsonRequestBodyMapper,
    ) {
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
        $this->jsonRequestBodyMapper->mapFromRequestBodyWithJson($request, $object);

        $errors = $this->validator->validate($object);
        if ($errors->count() > 0) {
            throw ValidationViolationsBadRequestException::create($request, $errors);
        }

        return $object;
    }
}
