<?php

use Laravel\Mcp\Facades\Mcp;
use App\Mcp\Servers\ServiceDeskServer;

Mcp::web('/mcp/tickets', ServiceDeskServer::class)
    ->middleware(['auth:sanctum']);

    //Falta por isso &reopened=false&page=1&ItemsPerPage=5
    // 1º Ou utilizar template
    // 2º
