<?php

use Laravel\Mcp\Facades\Mcp;
use App\Mcp\Servers\TicketsServer;
use App\Mcp\Servers\CategoriesServer;

Mcp::web('/mcp/tickets', TicketsServer::class)
    ->middleware(['auth:sanctum']);

Mcp::web('/mcp/categories', CategoriesServer::class)
    ->middleware(['auth:sanctum']);
