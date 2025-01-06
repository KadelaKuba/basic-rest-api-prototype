<?php

namespace App\Application\Model\Task\Exception;

use App\Components\Http\HttpOptions;
use Exception;

class TaskNotFoundException extends Exception
{
    private function __construct(
        string $message,
    ) {
        parent::__construct($message, HttpOptions::STATUS_NOT_FOUND);
    }

    public static function create(): self
    {
        return new self('Task not found by id');
    }
}
