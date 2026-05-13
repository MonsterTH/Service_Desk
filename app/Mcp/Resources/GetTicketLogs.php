<?php

namespace App\Mcp\Resources;

use App\Models\Ticket;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Resource;
use Laravel\Mcp\Server\Attributes\Description;

#[Description('Retrieve logs/history for a ticket')]
class GetTicketLogs extends Resource
{
    public function handle(Request $request): Response
    {
        $ticketId = $request->get('ticket_id');

        $ticket = Ticket::with('logs.user')->find($ticketId);

        if (! $ticket) {
            return Response::error('Ticket not found');
        }

        return Response::json([
            'ticket_id' => $ticket->id,
            'logs' => $ticket->logs->map(function ($log) {
                return [
                    'id' => $log->id,
                    'action' => $log->action,
                    'old_value' => $log->old_value,
                    'new_value' => $log->new_value,
                    'created_at' => $log->created_at,
                    'user' => [
                        'id' => $log->user?->id,
                        'name' => $log->user?->name,
                        'email' => $log->user?->email,
                    ],
                ];
            }),
        ]);
    }

    public function schema(\Illuminate\Contracts\JsonSchema\JsonSchema $schema): array
    {
        return [
            'ticket_id' => $schema->integer(),
        ];
    }
}
