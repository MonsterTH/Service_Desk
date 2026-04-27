<?php

namespace App\Mcp\Resources;

use App\Models\Ticket;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Resource;
use Laravel\Mcp\Server\Attributes\Description;

#[Description('Get a single ticket by ID')]
class GetTicket extends Resource
{
    public function handle(Request $request): Response
    {
        $data = $request->validate([
            'ticket_id' => 'required|exists:tickets,id',
        ]);

        $ticket = Ticket::with(['category', 'creator', 'assignee'])
            ->findOrFail($data['ticket_id']);

        return Response::json([
            'ticket' => [
                'id' => $ticket->id,
                'title' => $ticket->title,
                'description' => $ticket->description,
                'status' => $ticket->status,
                'priority' => $ticket->priority,
                'category' => $ticket->category?->name,
                'assigned_to' => $ticket->assigned_to,
                'created_by' => $ticket->created_by,
            ]
        ]);
    }

    public function schema(\Illuminate\Contracts\JsonSchema\JsonSchema $schema): array
    {
        return [
            'ticket_id' => $schema->integer()->required(),
        ];
    }
}
