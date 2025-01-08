<?php

declare(strict_types=1);

use App\Application\Actions\DefaultAction;
use Psr\Container\ContainerInterface;
use Slim\Interfaces\RouteCollectorProxyInterface;

return function (RouteCollectorProxyInterface $collector, ContainerInterface $container): void {
    $collector->get(DefaultAction::ACTION_PATH, DefaultAction::class)
        ->setName(DefaultAction::routeName());
};
