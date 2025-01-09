<?php

namespace App\Application\Model\Task;

use DateTimeImmutable;

class TaskData
{
    private function __construct(
        public string $title,
        public ?string $description,
        public TaskStatus $taskStatus,
        public DateTimeImmutable $createdAt,
        public DateTimeImmutable $updatedAt,
    ) {
    }

    public static function create(
        string $title,
        ?string $description,
        TaskStatus $taskStatus,
        DateTimeImmutable $createdAt,
        DateTimeImmutable $updatedAt,
    ): TaskData {
        return new self(
            $title,
            $description,
            $taskStatus,
            $createdAt,
            $updatedAt,
        );
    }
}
