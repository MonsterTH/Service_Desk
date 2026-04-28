<?php

namespace App\Mcp\Servers;

use Laravel\Mcp\Server;
use Laravel\Mcp\Server\Attributes\Instructions;
use Laravel\Mcp\Server\Attributes\Name;
use Laravel\Mcp\Server\Attributes\Version;
use App\Mcp\Tools\CreateTicket;
use App\Mcp\Tools\DeleteTicket;
use App\Mcp\Tools\UpdateTicket;
use App\Mcp\Tools\GetTicket;
use App\Mcp\Resources\GetAllTickets;

#[Name('TicketsServer')]
#[Version('0.0.1')]
#[Instructions('This server allows managing support tickets. Use CreateTicket to create tickets. Use DeleteTicket to soft delete (only when explicitly requested). Always confirm before deleting. Use UpdateTicket to update. Use GetTicket to get a single ticket.')]
class TicketsServer extends Server
{
    protected array $tools = [
        CreateTicket::class,
        DeleteTicket::class,
        UpdateTicket::class,
        GetTicket::class,
    ];

    protected array $resources = [
        GetAllTickets::class,
    ];

    protected array $prompts = [];
}
