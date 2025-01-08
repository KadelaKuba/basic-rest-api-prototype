<?php

declare(strict_types=1);

namespace App;

class Environment
{
    public const KEY_ENV = 'APP_ENV';

    public const ENV_PROD = 'prod';
    public const ENV_DEV = 'dev';
    public const ENV_TEST = 'test';

    public const ALLOWED_ENVIRONMENTS = [
        self::ENV_PROD,
        self::ENV_DEV,
        self::ENV_TEST,
    ];

    public function __construct(
        private string $environment
    ) {
        if (!in_array($environment, self::ALLOWED_ENVIRONMENTS, true)) {
            throw new \InvalidArgumentException(
                sprintf(
                    'Invalid environment (%s) value provided, given `%s`, allowed: `%s`',
                    self::KEY_ENV,
                    $environment,
                    implode(', ', self::ALLOWED_ENVIRONMENTS)
                )
            );
        }
    }

    public static function create(string $environment): self
    {
        return new self($environment);
    }

    public static function createWithServerAppEnv(): self
    {
        return new self($_SERVER[self::KEY_ENV]);
    }

    public static function createTestEnvironment(): self
    {
        return new self(self::ENV_TEST);
    }

    public static function createDevelopmentEnvironment(): self
    {
        return new self(self::ENV_DEV);
    }

    public static function createProductionEnvironment(): self
    {
        return new self(self::ENV_PROD);
    }

    public function isTestEnvironment(): bool
    {
        return $this->environment === self::ENV_TEST;
    }

    public function isDevelopmentEnvironment(): bool
    {
        return $this->environment === self::ENV_DEV;
    }

    public function isProductionEnvironment(): bool
    {
        return $this->environment === self::ENV_PROD;
    }

    public function getAsString(): string
    {
        return $this->environment;
    }
}
