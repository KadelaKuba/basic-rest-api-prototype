<?php

namespace App\Application\Api\Request\Task;

use App\Application\Model\Task\TaskStatus;
use OpenApi\Attributes as OA;
use Symfony\Component\Validator\Constraints as Assert;

trait TaskBodyTrait
{
    #[OA\Property(default: "title")]
    #[Assert\NotBlank]
    public string $title;

    #[OA\Property(default: "")]
    public string $description;

    #[OA\Property(default: "todo")]
    #[Assert\NotBlank]
    #[Assert\Choice(callback: [TaskStatus::class, 'values'])]
    public string $status;

    #[OA\Property(type: "string", format: "date-time", default: "2025-01-01 12:12:12")]
    #[Assert\NotBlank]
    #[Assert\DateTime]
    public string $created_at;

    #[OA\Property(type: "string", format: "date-time", default: "2025-01-01 12:12:12")]
    #[Assert\NotBlank]
    #[Assert\DateTime]
    public string $updated_at;
}
