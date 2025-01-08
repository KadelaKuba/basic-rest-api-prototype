<?php

namespace App\Application\Response\Task;

use App\Application\Model\Task\Task;
use OpenApi\Attributes as OA;

#[OA\Schema(schema: 'Response.TaskResponse')]
#[OA\Schema(schema: 'Response.ArrayTaskResponse', type: 'array', items: new OA\Items('#/components/schemas/Response.TaskResponse'))]
class TaskResponse
{
    public function __construct(
        #[OA\Property]
        public int $id,
        #[OA\Property]
        public string $title,
        #[OA\Property]
        public ?string $description,
        #[OA\Property]
        public string $status,
        #[OA\Property]
        public string $createdAt,
        #[OA\Property]
        public string $updatedAt
    ) {
    }

    public static function fromTask(Task $task): self
    {
        return new self(
            $task->getId(),
            $task->getTitle(),
            $task->getDescription(),
            $task->getTaskStatus()->value,
            $task->getCreatedAt()->format('Y-m-d H:i:s'),
            $task->getUpdatedAt()->format('Y-m-d H:i:s'),
        );
    }
}
