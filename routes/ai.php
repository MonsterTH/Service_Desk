<?php

use Laravel\Mcp\Facades\Mcp;
use App\Mcp\Servers\TicketsServer;
use App\Mcp\Servers\CategoriesServer;
use App\Mcp\Servers\CommentsServer;

Mcp::web('/api/mcp/tickets', TicketsServer::class)
    ->middleware(['auth:sanctum']);

Mcp::web('/api/mcp/categories', CategoriesServer::class)
    ->middleware(['auth:sanctum']);

Mcp::web('/api/mcp/comments', CommentsServer::class)
    ->middleware(['auth:sanctum']);
