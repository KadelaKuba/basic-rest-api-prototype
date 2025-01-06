<?php

declare(strict_types=1);

use App\Application\Actions\Api\CreateTaskAction;
use App\Application\Actions\Api\GetTaskAction;
use App\Application\Actions\Api\UpdateTaskAction;
use App\Application\Actions\DefaultAction;
use Psr\Container\ContainerInterface;
use Slim\Interfaces\RouteCollectorProxyInterface;

return function (RouteCollectorProxyInterface $collector, ContainerInterface $container): void {
    $collector->post(CreateTaskAction::ACTION_PATH, CreateTaskAction::class)
        ->setName(CreateTaskAction::routeName());

    $collector->get(GetTaskAction::ACTION_PATH, GetTaskAction::class)
        ->setName(GetTaskAction::routeName());

    $collector->put(UpdateTaskAction::ACTION_PATH, UpdateTaskAction::class)
        ->setName(UpdateTaskAction::routeName());

    $collector->get(DefaultAction::ACTION_PATH, DefaultAction::class)
        ->setName(DefaultAction::routeName());
};
