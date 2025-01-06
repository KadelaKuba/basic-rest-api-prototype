<?php

declare(strict_types=1);

namespace App\Application\Api\Actions;

abstract class AbstractAction
{
    public static function routeName(): string
    {
        return static::class;
    }
}
