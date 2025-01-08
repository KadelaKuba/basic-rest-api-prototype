<?php

namespace App\Application\Api\Request\Task;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: "Request.Task.CreateTaskBody",
    required: ['title', 'status', 'created_at', 'updated_at']
)]
class CreateTaskBody
{
    use TaskBodyTrait;
}
