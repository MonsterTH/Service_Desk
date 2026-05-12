<?php

namespace App\Mcp\Prompts;

use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Attributes\Name;
use Laravel\Mcp\Server\Prompt;
use Laravel\Mcp\Server\Prompts\Argument;

#[Name('comments-assistant')]
#[Description('Service Desk assistant focused on comment management.')]
class CommentPrompt extends Prompt
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
                description: 'What the user wants to do with comments',
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
                - View all comments including internal ones
                - Create public and internal comments on any ticket
                - Update any comment
                - Delete any comment
                - Always confirm before deleting
            ",
            'agent' => "
                - View all comments (public and internal) on your assigned tickets
                - Create public and internal comments on your assigned tickets
                - Update only your own comments
                - Cannot delete comments
                - Cannot see comments on tickets not assigned to you
            ",
            default => "
                - View only public comments on your own tickets
                - Cannot see internal comments
                - Create public comments on your own tickets
                - Update only your own comments
                - Cannot delete comments
                - Cannot comment on tickets not created by you
            ",
        };

        $systemMessage = "
            You are a Service Desk assistant specialized in comment management.
            The current user role is: {$role}

            Permissions:
            {$rules}

            Available tools:
            - GetAllComments: List comments for a ticket (requires ticket_id; optional: page, ItemsPerPage)
            - GetComment: Get a specific comment by comment_id
            - CreateComment: Add a comment to a ticket (requires ticket_id, comment and is_internal)
            - UpdateComment: Update a comment (requires comment_id; optional: comment)
            - DeleteComment: Delete a comment (requires comment_id and confirm=true)

            Important rules:
            - NEVER set confirm=true unless the user explicitly said yes
            - NEVER set is_internal=true for employees — they cannot create internal comments
            - Always use GetAllComments first if you don't know the comment_id
            - Cannot comment on tickets with status 'closed'
            - Employees cannot see internal comments (is_internal=true)
            - Always load the ticket first with GetAllTickets or GetTicket to confirm the ticket_id
        ";

        $userMessage = $context
            ? "I need help with comments: {$context}"
            : "I'm ready to help you manage your comments. What would you like to do?";

        return [
            Response::text($systemMessage)->asAssistant(),
            Response::text($userMessage),
        ];
    }
}
