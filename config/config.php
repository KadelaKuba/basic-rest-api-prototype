<?php

declare(strict_types=1);

use App\Application\Api\Slim\JsonMappedBodyStrategy;
use App\ContainerDefinitions;
use App\Environment;
use DI\ContainerBuilder;
use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\ORMSetup;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Log\LoggerInterface;
use Slim\CallableResolver;
use Slim\Factory\AppFactory;
use Slim\Interfaces\CallableResolverInterface;
use Slim\Interfaces\RouteCollectorInterface;
use Slim\Logger;
use Slim\Routing\RouteCollector;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Validator\ValidatorInterface;

return function (ContainerBuilder $builder, string $environment) {
    // file and folder paths
    $builder->addDefinitions(
        [
            ContainerDefinitions::PATH_ROOT => fn (): string => dirname(__DIR__),
            ContainerDefinitions::PATH_SRC_MODEL => fn (): string => dirname(__DIR__) . '/src/Application/Model',
            ContainerDefinitions::PATH_LOGS => fn (): string => dirname(__DIR__) . '/logs',
        ]
    );

    // add parameters based on environment
    $builder->addDefinitions(
        [
            ContainerDefinitions::APP_ENV => $environment,
            ContainerDefinitions::APP_DEBUG => DI\env('APP_DEBUG'),
            ContainerDefinitions::DATABASE_HOST => DI\env('DB_HOST'),
            ContainerDefinitions::DATABASE_NAME => DI\env('DB_DATABASE'),
            ContainerDefinitions::DATABASE_USER => DI\env('DB_USER'),
            ContainerDefinitions::DATABASE_PASSWORD => DI\env('DB_USER_PASSWORD'),
            ContainerDefinitions::DATABASE_PORT => DI\env('DB_PORT'),
        ]
    );

    // add main services
    $builder->addDefinitions(
        [
            Environment::class => function (ContainerInterface $container): Environment {
                return Environment::create($container->get(ContainerDefinitions::APP_ENV));
            },
            EntityManagerInterface::class => DI\get(EntityManager::class),
            ResponseFactoryInterface::class => fn () => AppFactory::determineResponseFactory(),
            CallableResolverInterface::class => fn (ContainerInterface $container) => new CallableResolver($container),
            RouteCollectorInterface::class => function (ContainerInterface $container): RouteCollectorInterface {
                return new RouteCollector(
                    $container->get(ResponseFactoryInterface::class),
                    $container->get(CallableResolverInterface::class),
                    $container,
                    $container->get(JsonMappedBodyStrategy::class),
                );
            },
            LoggerInterface::class => fn (ContainerInterface $container) => new Logger(),
            EntityManager::class => function (ContainerInterface $container): EntityManager {
                $config = ORMSetup::createAttributeMetadataConfiguration(
                    [
                        $container->get(ContainerDefinitions::PATH_SRC_MODEL),
                    ],
                    true,
                );

                $databaseParameters = [
                    'driver' => 'pdo_mysql',
                    'host' => $container->get(ContainerDefinitions::DATABASE_HOST),
                    'dbname' => $container->get(ContainerDefinitions::DATABASE_NAME),
                    'user' => $container->get(ContainerDefinitions::DATABASE_USER),
                    'password' => $container->get(ContainerDefinitions::DATABASE_PASSWORD),
                    'charset' => 'utf8',
                ];
                $databaseParameters = DriverManager::getConnection($databaseParameters, $config);
                $entityManager = new EntityManager($databaseParameters, $config);

                return $entityManager;
            },
            ValidatorInterface::class => function (ContainerInterface $container): ValidatorInterface {
                return Validation::createValidatorBuilder()
                    ->enableAttributeMapping()
                    ->getValidator();
            },
        ]
    );
};
