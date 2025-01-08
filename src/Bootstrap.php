<?php

declare(strict_types=1);

namespace App;

use DI\ContainerBuilder;
use Doctrine\Migrations\Configuration\EntityManager\ExistingEntityManager;
use Doctrine\Migrations\Configuration\Migration\PhpFile;
use Doctrine\Migrations\DependencyFactory;
use Doctrine\Migrations\Tools\Console\ConsoleRunner as MigrationsConsoleRunner;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Console\ConsoleRunner;
use Doctrine\ORM\Tools\Console\EntityManagerProvider\SingleManagerProvider;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Log\LoggerInterface;
use Slim\App;
use Slim\Handlers\ErrorHandler;
use Slim\Interfaces\CallableResolverInterface;
use Slim\Interfaces\RouteCollectorInterface;
use Slim\Interfaces\RouteCollectorProxyInterface;
use Symfony\Component\Console\Application;

class Bootstrap
{
    /**
     * @var ContainerBuilder<\DI\Container> $containerBuilder
     */
    private ContainerBuilder $containerBuilder;

    /**
     * @var callable
     */
    private $extraDefinitions;

    /**
     * @param \DI\ContainerBuilder<\DI\Container>|null $containerBuilder
     */
    private function __construct(
        private Environment $environment,
        ?ContainerBuilder $containerBuilder = null,
        ?callable $extraDefinitions = null
    ) {
        $this->containerBuilder = $containerBuilder ?? new ContainerBuilder();

        if ($extraDefinitions === null) {
            $this->extraDefinitions = static function (ContainerBuilder $containerBuilder): void {
                // no op
            };
        } else {
            $this->extraDefinitions = $extraDefinitions;
        }
    }

    public static function createWithServerAppEnv(): self
    {
        return new self(Environment::createWithServerAppEnv());
    }

    public function buildContainer(): ContainerInterface
    {
        $addSettings = require __DIR__ . '/../config/config.php';
        $addSettings($this->containerBuilder, $this->environment->getAsString());

        ($this->extraDefinitions)($this->containerBuilder);

        return $this->containerBuilder->build();
    }

    private function buildRouter(RouteCollectorProxyInterface $routeCollector, ContainerInterface $container): void
    {
        $routerSettings = require __DIR__ . '/../config/router.php';
        $routerSettings($routeCollector, $container);
    }


    public function createSlim(ContainerInterface $container): App
    {
        $slimApplication = new App(
            $container->get(ResponseFactoryInterface::class),
            $container,
            $container->get(CallableResolverInterface::class),
            $container->get(RouteCollectorInterface::class)
        );

        $isDebugEnabled = (bool)$container->get(ContainerDefinitions::APP_DEBUG);
        $errorMiddleware = $slimApplication->addErrorMiddleware(
            $isDebugEnabled,
            true,
            true,
            $container->get(LoggerInterface::class),
        );

        $errorHandler = new ErrorHandler(
            $slimApplication->getCallableResolver(),
            $slimApplication->getResponseFactory(),
            $container->get(LoggerInterface::class),
        );

        $errorMiddleware->setDefaultErrorHandler($errorHandler);

        $this->buildRouter($slimApplication, $container);

        return $slimApplication;
    }

    public function createConsole(ContainerInterface $container): Application
    {
        $app = new Application('Basic REST API for managing tasks');

        $entityManager = $container->get(EntityManager::class);
        ConsoleRunner::addCommands($app, new SingleManagerProvider($entityManager));

        $dependencyFactory = DependencyFactory::fromEntityManager(
            new PhpFile(__DIR__ . '/../config/doctrine-migrations.php'),
            new ExistingEntityManager($entityManager),
        );

        MigrationsConsoleRunner::addCommands($app, $dependencyFactory);

        return $app;
    }

    /**
     * @internal for tests only
     */
    public function getEnvironment(): Environment
    {
        return $this->environment;
    }
}
