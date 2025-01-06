<?php

namespace App\Application\Model\Task;

use App\Application\Api\Request\Task\CreateTaskBody;
use App\Application\Api\Request\Task\UpdateTaskBody;

class TaskDataFactory
{
    public function createFromCreateTaskBody(CreateTaskBody $createTaskBody): TaskData
    {
        return TaskData::create(
            $createTaskBody->title,
            $createTaskBody->description,
            TaskStatus::from($createTaskBody->status),
            new \DateTimeImmutable($createTaskBody->created_at),
            new \DateTimeImmutable($createTaskBody->updated_at),
        );
    }

    public function createFromUpdateTaskBody(UpdateTaskBody $updateTaskBody): TaskData
    {
        return TaskData::create(
            $updateTaskBody->title,
            $updateTaskBody->description,
            TaskStatus::from($updateTaskBody->status),
            new \DateTimeImmutable($updateTaskBody->created_at),
            new \DateTimeImmutable($updateTaskBody->updated_at),
        );
    }
}
