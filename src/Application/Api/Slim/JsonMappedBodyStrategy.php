<?php

namespace App\Application\Api\Slim;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use ReflectionMethod;
use Slim\Interfaces\InvocationStrategyInterface;

class JsonMappedBodyStrategy implements InvocationStrategyInterface
{
    public function __construct(
        private ActionParameterResolver $actionParameterResolver,
    ) {
    }

    public function __invoke(callable $callable, ServerRequestInterface $request, ResponseInterface $response, array $routeArguments): ResponseInterface
    {
        $actionReflection = new ReflectionMethod($callable[0], '__invoke');

        $actionArguments = $this->actionParameterResolver->resolveActionParameters(
            $actionReflection,
            $request,
            $response,
            $routeArguments
        );

        return $callable(...$actionArguments);
    }
}
