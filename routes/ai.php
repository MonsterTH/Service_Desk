<?php

use Laravel\Mcp\Facades\Mcp;
use App\Mcp\Servers\ServiceDeskServer;

Mcp::web('/mcp/tickets', ServiceDeskServer::class)
    ->middleware(['auth:sanctum']);
