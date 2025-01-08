<?php

namespace App\Application\Api;

use OpenApi\Attributes as OA;

#[OA\OpenApi(openapi: "3.1.0")]
#[OA\Info(version: "1.0", description: "Api endpoints for task managing", title: "Task API")]
#[OA\Server(url: "{hostUrl}", description: "Api host URL", variables: [
    new OA\ServerVariable(serverVariable: "hostUrl", description: "Keep empty to be used for URL from where file was served", default: "http://localhost:8090")
])]
class Api
{
}
