<?php

declare(strict_types=1);

use App\Application\Api\Actions\Task\CreateTaskAction;
use App\Application\Api\Actions\Task\GetTaskAction;
use App\Application\Api\Actions\Task\UpdateTaskAction;
use App\Middlewares\Api\ApiErrorMiddleware;
use Psr\Container\ContainerInterface;
use Slim\Interfaces\RouteCollectorProxyInterface;

return function (RouteCollectorProxyInterface $collector, ContainerInterface $container): void {
    $apiRoutesGroup = $collector->group('', function (RouteCollectorProxyInterface $collector) {
        $collector->post(CreateTaskAction::ACTION_PATH, CreateTaskAction::class)
            ->setName(CreateTaskAction::routeName());

        $collector->get(GetTaskAction::ACTION_PATH, GetTaskAction::class)
            ->setName(GetTaskAction::routeName());

        $collector->put(UpdateTaskAction::ACTION_PATH, UpdateTaskAction::class)
            ->setName(UpdateTaskAction::routeName());
    });

    $apiRoutesGroup
        ->addMiddleware($container->get(ApiErrorMiddleware::class));
};
