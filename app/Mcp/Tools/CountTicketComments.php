<?php

namespace App\Mcp\Tools;

use App\Models\Comment;
use App\Models\Ticket;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Tool;
use Laravel\Mcp\Server\Attributes\Description;

#[Description('Get comments count for a ticket')]
class CountTicketComments extends Tool
{
    public function handle(Request $request): Response
    {
        /** @var \App\Models\User $user */
        $user = $request->user();

        $ticket = Ticket::find($request->get('ticket_id'));

        if (! $ticket) {
            return Response::error('Ticket not found');
        }

        if (! $user->can('count', [Comment::class, $ticket])) {
            return Response::error('Unauthorized');
        }

        return Response::json([
            'ticket_id' => $ticket->id,
            'counts' => [
                'total' => $ticket->comments()->count(),
                'public' => $ticket->comments()
                    ->where('is_internal', false)
                    ->count(),

                'internal' => $ticket->comments()
                    ->where('is_internal', true)
                    ->count(),
            ]
        ]);
    }

    public function schema(
        \Illuminate\Contracts\JsonSchema\JsonSchema $schema
    ): array {
        return [
            'ticket_id' => $schema->integer(),
        ];
    }
}
