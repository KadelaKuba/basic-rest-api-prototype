<?php

namespace App\Application\Api\Request\Task;

use App\Application\Model\Task\TaskStatus;
use Symfony\Component\Validator\Constraints as Assert;

trait TaskBodyTrait
{
    #[Assert\NotBlank]
    public string $title;

    public string $description;

    #[Assert\NotBlank]
    #[Assert\Choice(callback: [TaskStatus::class, 'values'])]
    public string $status;

    #[Assert\NotBlank]
    #[Assert\DateTime]
    public string $created_at;

    #[Assert\NotBlank]
    #[Assert\DateTime]
    public string $updated_at;
}
