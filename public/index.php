<?php

declare(strict_types=1);

use App\Bootstrap;
use Symfony\Component\Dotenv\Dotenv;

require_once __DIR__ . '/../vendor/autoload.php';

(new Dotenv())->bootEnv(__DIR__ . '/../.env');

$bootstrap = Bootstrap::createWithServerAppEnv();
$bootstrap->createSlim($bootstrap->buildContainer())
    ->run();
