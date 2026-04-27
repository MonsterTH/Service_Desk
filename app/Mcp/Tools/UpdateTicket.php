<?php

namespace App\Mcp\Tools;

use App\Models\Ticket;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Tool;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

#[Description('Update ticket (sync with API permissions: status, priority, assignment restricted).')]
class UpdateTicket extends Tool
{
    use AuthorizesRequests;

    public function handle(Request $request): Response
    {
        $user = $request->user();

        if (! $user) {
            return Response::error('Unauthorized');
        }

        $data = $request->validate([
            'ticket_id' => 'required|exists:tickets,id',

            'title' => 'sometimes|string|max:255',
            'description' => 'sometimes|nullable|string',

            'status' => 'sometimes|in:open,in_progress,resolved,closed',
            'priority' => 'sometimes|in:low,medium,high,urgent',
            'assigned_to' => 'sometimes|nullable|exists:users,id',
        ]);

        $ticket = Ticket::findOrFail($data['ticket_id']);

        // 1. regra base (igual controller)
        $this->authorize('update', $ticket);

        // 2. regras específicas já existentes no teu sistema
        if (isset($data['status'])) {
            $this->authorize('updateStatus', $ticket);
        }

        if (isset($data['priority'])) {
            $this->authorize('updatePriority', $ticket);
        }

        if (isset($data['assigned_to'])) {
            $targetUser = \App\Models\User::findOrFail($data['assigned_to']);
            $this->authorize('assign', [$ticket, $targetUser]);
        }

        // 3. aplicar update
        $ticket->update(collect($data)->except('ticket_id')->toArray());

        return Response::json([
            'success' => true,
            'ticket' => $ticket->fresh(['category', 'creator', 'assignee']),
        ]);
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'ticket_id' => $schema->integer()->required(),

            'title' => $schema->string()->nullable(),
            'description' => $schema->string()->nullable(),

            'status' => $schema->string()->nullable(),
            'priority' => $schema->string()->nullable(),

            'assigned_to' => $schema->integer()->nullable(),
        ];
    }
}
