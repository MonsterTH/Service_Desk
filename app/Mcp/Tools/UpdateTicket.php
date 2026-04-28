<?php

namespace App\Mcp\Tools;

use App\Models\Ticket;
use App\Models\User;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Tool;

#[Description('Update ticket fields. Status, priority and assignment follow API permission rules.')]
class UpdateTicket extends Tool
{
    public function handle(Request $request): Response
    {
        /** @var \App\Models\User $user */
        $user = $request->user();

        if (!$user) {
            return Response::error('Unauthorized');
        }

        $data = $request->validate([
            'ticket_id'   => 'required|exists:tickets,id',
            'title'       => 'sometimes|string|max:255',
            'description' => 'sometimes|nullable|string',
            'status'      => 'sometimes|in:open,in_progress,resolved,closed',
            'priority'    => 'sometimes|in:low,medium,high,urgent',
            'assigned_to' => 'sometimes|nullable|exists:users,id',
        ]);

        $ticket = Ticket::findOrFail($data['ticket_id']);

        if ($ticket->isFinalState()) {
            return Response::error('Ticket is closed and cannot be updated.');
        }

        $canUpdate = $user->hasRole('admin')
            || ($user->hasRole('agent') && $ticket->assigned_to === $user->id)
            || ($user->hasRole('employee') && $ticket->created_by === $user->id);

        if (!$canUpdate) {
            return Response::error('You do not have permission to update this ticket.');
        }

        if (isset($data['status'])) {
            $canUpdateStatus = $user->hasRole('admin')
                || ($user->hasRole('agent') && $ticket->assigned_to === $user->id);

            if (!$canUpdateStatus) {
                return Response::error('You do not have permission to update the status.');
            }

            if (!$ticket->canTransitionTo($data['status'])) {
                return Response::error("Cannot transition from {$ticket->status} to {$data['status']}.");
            }
        }

        if (isset($data['priority'])) {
            $canUpdatePriority = $user->hasRole('admin')
                || ($user->hasRole('agent') && $ticket->assigned_to === $user->id);

            if (!$canUpdatePriority) {
                return Response::error('You do not have permission to update the priority.');
            }
        }

        if (isset($data['assigned_to'])) {
            $targetUser = User::findOrFail($data['assigned_to']);

            $canAssign = ($user->hasRole('admin') && $targetUser->hasRole('agent'))
                || ($user->hasRole('agent') && $targetUser->id === $user->id);

            if (!$canAssign) {
                return Response::error('You do not have permission to assign this ticket.');
            }
        }

        $ticket->update(collect($data)->except('ticket_id')->toArray());

        return Response::json([
            'success' => true,
            'ticket'  => $ticket->fresh(['category', 'creator', 'assignee']),
        ]);
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'ticket_id'   => $schema->integer()->required(),
            'title'       => $schema->string()->nullable(),
            'description' => $schema->string()->nullable(),
            'status'      => $schema->string()->nullable(),
            'priority'    => $schema->string()->nullable(),
            'assigned_to' => $schema->integer()->nullable(),
        ];
    }
}
