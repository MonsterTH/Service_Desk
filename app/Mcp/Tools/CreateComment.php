<?php

namespace App\Mcp\Tools;

use App\Models\Comment;
use App\Models\Ticket;
use App\Http\Resources\CommentResource;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Tool;

#[Description('Create a comment on a ticket (public or internal depending on role).')]
class CreateComment extends Tool
{
    public function handle(Request $request): Response
    {
        /** @var \App\Models\User $user */
        $user = $request->user();

        if (!$user) {
            return Response::error('Unauthorized.');
        }

        $data = $request->validate([
            'ticket_id'   => 'required|exists:tickets,id',
            'comment'     => 'required|string',
            'is_internal' => 'sometimes|boolean',
        ]);

        $ticket = Ticket::findOrFail($data['ticket_id']);

        // ✅ Replica lógica da CommentPolicy::create()
        if ($ticket->isFinalState()) {
            return Response::error('Cannot comment on a closed ticket.');
        }

        $canComment = $user->hasRole('admin')
            || ($user->hasRole('employee') && $ticket->created_by === $user->id)
            || ($user->hasRole('agent') && $ticket->assigned_to === $user->id);

        if (!$canComment) {
            return Response::error('You do not have permission to comment on this ticket.');
        }

        // ✅ Employee nunca pode criar comentário interno
        $isInternal = false;
        if (($data['is_internal'] ?? false) === true) {
            if (!$user->hasAnyRole(['admin', 'agent'])) {
                return Response::error('Only agents and admins can create internal comments.');
            }
            $isInternal = true;
        }

        $comment = Comment::create([
            'ticket_id'   => $ticket->id,
            'user_id'     => $user->id,
            'comment'     => $data['comment'],
            'is_internal' => $isInternal,
        ]);

        return Response::json([
            'success' => true,
            'comment' => new CommentResource($comment->load(['ticket', 'user'])),
        ]);
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'ticket_id' => $schema->integer()
                ->description('Ticket ID where comment will be added')
                ->required(),
            'comment' => $schema->string()
                ->description('Comment content')
                ->required(),
            'is_internal' => $schema->boolean()
                ->description('Only agents/admins can set internal comments')
                ->required(),
        ];
    }
}
