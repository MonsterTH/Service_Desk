<?php

namespace App\Http\Controllers;
use OpenApi\Attributes as OA;

#[OA\OpenApi(
    openapi: "3.1.0"
)]
#[OA\Info(
    title: "Service Desk API",
    version: "1.0.0",
    description: "API documentation"
)]
#[OA\Server(
    url: "http://localhost:8000",
    description: "API Server"
)]

abstract class Controller
{
    //
}
