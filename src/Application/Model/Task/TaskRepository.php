<?php

namespace App\Application\Model\Task;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

class TaskRepository
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager
    ) {
    }

    /**
     * @return \Doctrine\ORM\EntityRepository<Task>
     */
    private function getTaskRepository(): EntityRepository
    {
        return $this->entityManager->getRepository(Task::class);
    }

    private function createAllQueryBuilder(): QueryBuilder
    {
        return $this->getTaskRepository()
            ->createQueryBuilder('task');
    }

    /**
     * @return Task[]
     */
    public function getAllTasks(): array
    {
        return $this->createAllQueryBuilder()
            ->getQuery()
            ->getArrayResult();
    }

    public function getById(int $id): Task
    {
        return $this->createAllQueryBuilder()
            ->andWhere('task.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
