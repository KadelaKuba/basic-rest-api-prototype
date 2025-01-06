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
class Task implements JsonSerializable
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

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'taskStatus' => $this->taskStatus,
            'createdAt' => $this->createdAt,
            'updatedAt' => $this->updatedAt,
        ];
    }
}
