<?php

namespace App\Application\Model\Task;

use DateTimeImmutable;

class TaskData
{
    public function __construct(
        public string $title,
        public ?string $description,
        public TaskStatus $taskStatus,
        public DateTimeImmutable $createdAt,
        public DateTimeImmutable $updatedAt,
    ) {
    }
}
