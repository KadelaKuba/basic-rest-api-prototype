<?php

namespace App\Application\Model\Task;

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
        return $this->taskRepository->getById($id);
    }

    public function edit(int $taskId, TaskData $taskData): Task
    {
        $task = $this->getById($taskId);
        $task->edit($taskData);

        $this->entityManager->flush();

        return $task;
    }
}
