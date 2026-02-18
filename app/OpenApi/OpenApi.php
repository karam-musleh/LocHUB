<?php

namespace App\OpenApi;

use OpenApi\Attributes as OA;

#[OA\Info(
    version: "1.0.0",
    title: "LocHUB API",
    description: "API Documentation for LocHUB project"
)]
#[OA\Server(
    url: "http://localhost:8000/api",
    description: "Local API Server"
)]
class OpenApi {}

