<?php

namespace App\Application\Model\Task;

use DateTimeImmutable;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\Table;
use JsonSerializable;

#[Entity]
#[Table(name: 'tasks')]
class Task
{
    #[Id]
    #[Column(type: 'integer')]
    #[GeneratedValue]
    private int $id;

    #[Column(type: 'string')]
    private string $title;

    #[Column(type: 'string', nullable: true)]
    private ?string $description;

    #[Column(type: 'string', enumType: TaskStatus::class)]
    private TaskStatus $taskStatus;

    #[Column(type: 'datetime_immutable')]
    private DateTimeImmutable $createdAt;

    #[Column(type: 'datetime_immutable')]
    private DatetimeImmutable $updatedAt;

    private function __construct(TaskData $taskData)
    {
        $this->title = $taskData->title;
        $this->description = $taskData->description;
        $this->taskStatus = $taskData->taskStatus;
        $this->createdAt = $taskData->createdAt;
        $this->updatedAt = $taskData->updatedAt;
    }

    public function edit(TaskData $taskData): void
    {
        $this->title = $taskData->title;
        $this->description = $taskData->description;
        $this->taskStatus = $taskData->taskStatus;
        $this->createdAt = $taskData->createdAt;
        $this->updatedAt = $taskData->updatedAt;
    }

    public static function create(
        TaskData $taskData
    ): Task {
        return new self($taskData);
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getTaskStatus(): TaskStatus
    {
        return $this->taskStatus;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): DateTimeImmutable
    {
        return $this->updatedAt;
    }

    /**
     * @internal for testing purposes only
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }
}
