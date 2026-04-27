<?php

use Laravel\Mcp\Facades\Mcp;
use App\Mcp\Servers\TicketsServer;
use App\Mcp\Servers\CategoriesServer;
use App\Mcp\Servers\CommentsServer;

Mcp::web('/mcp/tickets', TicketsServer::class)
    ->middleware(['auth:sanctum']);

Mcp::web('/mcp/categories', CategoriesServer::class)
    ->middleware(['auth:sanctum']);

Mcp::web('/mcp/comments', CommentsServer::class)
    ->middleware(['auth:sanctum']);
