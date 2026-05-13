<?php

namespace App\Mcp\Tools;

use App\Models\Ticket;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Tool;
use Laravel\Mcp\Server\Attributes\Description;

#[Description('Rate a resolved or closed ticket')]
class RateTicket extends Tool
{
    public function handle(Request $request): Response
    {
        /** @var \App\Models\User $user */
        $user = $request->user();

        $ticket = Ticket::find($request->get('ticket_id'));

        if (! $ticket) {
            return Response::error('Ticket not found');
        }

        $ratingValue = (int) $request->get('rating');

        // validação do rating
        if ($ratingValue < 1 || $ratingValue > 5) {
            return Response::error(
                'Rating must be between 1 and 5.'
            );
        }

        if (! in_array($ticket->status, ['resolved', 'closed'])) {
            return Response::error(
                'Only resolved or closed tickets can be rated.'
            );
        }

        if ($ticket->rating) {
            return Response::error(
                'This ticket has already been rated.'
            );
        }

        $rating = $ticket->rating()->create([
            'user_id' => $user->id,
            'rating' => $ratingValue,
            'comment' => $request->get('comment'),
        ]);

        return Response::json([
            'message' => 'Rating created successfully',
            'rating' => [
                'id' => $rating->id,
                'rating' => $rating->rating,
                'comment' => $rating->comment,
            ]
        ]);
    }

    public function schema(\Illuminate\Contracts\JsonSchema\JsonSchema $schema): array
    {
        return [
            'ticket_id' => $schema->integer(),
            'rating' => $schema->integer(),
            'comment' => $schema->string()->nullable(),
        ];
    }
}
