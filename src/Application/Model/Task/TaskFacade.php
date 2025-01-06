<?php

namespace App\Application\Model\Task;

use App\Application\Model\Task\Exception\TaskNotFoundException;
use Doctrine\ORM\EntityManagerInterface;

class TaskFacade
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private TaskRepository $taskRepository,
    ) {
    }

    public function create(TaskData $taskData): Task
    {
        $task = Task::create($taskData);

        $this->entityManager->persist($task);
        $this->entityManager->flush();

        return $task;
    }

    /**
     * @return Task[]
     */
    public function getAllTasks(): array
    {
        return $this->taskRepository->getAllTasks();
    }

    public function getById(int $id): Task
    {
        $task = $this->taskRepository->findById($id);

        if ($task === null) {
            throw TaskNotFoundException::create();
        }

        return $task;
    }

    public function edit(int $taskId, TaskData $taskData): Task
    {
        $task = $this->getById($taskId);
        $task->edit($taskData);

        $this->entityManager->flush();

        return $task;
    }
}
