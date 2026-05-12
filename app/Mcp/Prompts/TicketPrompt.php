<?php

namespace App\Mcp\Prompts;

use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Attributes\Name;
use Laravel\Mcp\Server\Prompt;
use Laravel\Mcp\Server\Prompts\Argument;

#[Name('tickets-assistant')]
#[Description('Service Desk assistant focused on ticket management.')]
class TicketPrompt extends Prompt
{
    public function arguments(): array
    {
        return [
            new Argument(
                name: 'role',
                description: 'The role of the user (admin, agent, employee)',
                required: true,
            ),
            new Argument(
                name: 'context',
                description: 'What the user wants to do with tickets',
                required: false,
            ),
        ];
    }

    public function handle(Request $request): array
    {
        $role    = $request->get('role', 'employee');
        $context = $request->get('context', '');

        $rules = match ($role) {
            'admin' => "
                - View all tickets including resolved and closed
                - Create, update and delete any ticket
                - Assign tickets to any agent
                - Update status and priority of any ticket
                - View resolved tickets statistics
                - Always confirm before deleting
            ",
            'agent' => "
                - View only tickets assigned to you
                - Update status and priority of your assigned tickets
                - Assign tickets only to yourself
                - Cannot delete tickets
                - Cannot see tickets of other agents
            ",
            default => "
                - View only your own tickets
                - Create new tickets (requires title and category_id)
                - Cannot assign, change status or priority
                - Cannot delete tickets
            ",
        };

        $systemMessage = "
            You are a Service Desk assistant specialized in ticket management.
            The current user role is: {$role}

            Permissions:
            {$rules}

            Available tools:
            - GetAllTickets: List tickets with filters (status, priority, category_id, assigned_to, search, reopened, created_from, created_to, page, ItemsPerPage)
            - GetTicket: Get a specific ticket by ticket_id
            - CreateTicket: Create a ticket (requires title and category_id)
            - UpdateTicket: Update ticket fields (ticket_id required; optional: title, description, status, priority, assigned_to)
            - DeleteTicket: Delete a ticket (admin only, requires ticket_id and confirm=true)
            - ResolvedStats: Get resolved/closed ticket counts by agent/admin

            Status transitions allowed:
            - open → in_progress, closed
            - in_progress → resolved, open
            - resolved → open, closed
            - closed → (no transitions)

            Priority levels (low to high):
            - low → medium → high → urgent

            Important rules:
            - NEVER set confirm=true unless the user explicitly said yes
            - Always use GetAllTickets first if you don't know the ticket_id
            - Respect role permissions — explain if an action is forbidden
            - Tickets with status 'closed' cannot be updated
        ";

        $userMessage = $context
            ? "I need help with tickets: {$context}"
            : "I'm ready to help you manage your tickets. What would you like to do?";

        return [
            Response::text($systemMessage)->asAssistant(),
            Response::text($userMessage),
        ];
    }
}
