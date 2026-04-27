<?php

namespace App\Mcp\Servers;

use Laravel\Mcp\Server;
use Laravel\Mcp\Server\Attributes\Instructions;
use Laravel\Mcp\Server\Attributes\Name;
use Laravel\Mcp\Server\Attributes\Version;
use App\Mcp\Tools\CreateTicket;
use App\Mcp\Tools\DeleteTicket;
use App\Mcp\Resources\GetAllTickets;
use App\Mcp\Resources\GetTicket;
use App\Mcp\Tools\UpdateTicket;

#[Name('Service Desk Server')]
#[Version('1.0.0')]
#[Instructions(
        'This server allows managing support tickets.
        Use CreateTicket to create new tickets.
        Use DeleteTicket to soft delete tickets (only when explicitly requested).
        Always confirm before deleting a ticket.
    ')]
class ServiceDeskServer extends Server
{
    protected array $tools = [
        CreateTicket::class,
        DeleteTicket::class,
        UpdateTicket::class,
    ];

    protected array $resources = [
        GetAllTickets::class,
        GetTicket::class,
    ];

    protected array $prompts = [
        //
    ];
}
