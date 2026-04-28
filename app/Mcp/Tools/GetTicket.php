<?php

namespace App\Mcp\Tools;

use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Tool;
use App\Models\Ticket;
use Laravel\Mcp\Server\Tools\Annotations\IsReadOnly;

#[Description('Get a single ticket by ID')]
#[IsReadOnly]
class GetTicket extends Tool
{
    public function handle(Request $request): Response
    {
        /** @var \App\Models\User $user */
        $user = $request->user();

        if (!$user) {
            return Response::error('Unauthorized');
        }

        $data = $request->validate([
            'ticket_id' => 'required|exists:tickets,id',
        ]);

        $ticket = Ticket::with(['category', 'creator', 'assignee'])
            ->findOrFail($data['ticket_id']);

        $canView = $user->hasRole('admin')
            || ($user->hasRole('agent') && $ticket->assigned_to === $user->id)
            || $ticket->created_by === $user->id;

        if (!$canView) {
            return Response::error('You do not have permission to view this ticket.');
        }

        return Response::json([
            'ticket' => [
                'id'          => $ticket->id,
                'title'       => $ticket->title,
                'description' => $ticket->description,
                'status'      => $ticket->status,
                'priority'    => $ticket->priority,
                'category'    => $ticket->category?->name,
                'assigned_to' => $ticket->assignee?->name,
                'created_by'  => $ticket->creator?->name,
            ]
        ]);
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'ticket_id' => $schema->integer()->required(),
        ];
    }
}
