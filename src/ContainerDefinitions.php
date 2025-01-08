<?php

declare(strict_types=1);

namespace App;

interface ContainerDefinitions
{
    public const APP_ENV = 'app.env';
    public const APP_DEBUG = 'app.debug';

    public const PATH_ROOT = 'path.root';
    public const PATH_SRC_MODEL = 'path.src.model';
    public const PATH_LOGS = 'path.logs';

    public const DATABASE_HOST = 'database.host';
    public const DATABASE_NAME = 'database.name';
    public const DATABASE_USER = 'database.user';
    public const DATABASE_PASSWORD = 'database.password';
    public const DATABASE_PORT = 'database.port';
}
