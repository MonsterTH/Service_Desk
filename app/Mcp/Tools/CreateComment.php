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
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

#[Description('Create a comment on a ticket (public or internal depending on role).')]
class CreateComment extends Tool
{
    use AuthorizesRequests;

    public function handle(Request $request): Response
    {
        /** @var \App\Models\User $user */
        $user = $request->user();

        if (! $user) {
            return Response::error('Unauthorized');
        }

        $data = $request->validate([
            'ticket_id' => 'required|exists:tickets,id',
            'comment'   => 'required|string',
            'is_internal' => 'sometimes|boolean',
        ]);

        $ticket = Ticket::findOrFail($data['ticket_id']);

        // Policy
        $this->authorize('create', [Comment::class, $ticket]);

        // regra de segurança: employee nunca pode criar internal comment
        $isInternal = false;

        if (($data['is_internal'] ?? false) === true) {
            if (! $user->hasRole(['admin', 'agent'])) {
                return Response::error('Forbidden: internal comments only for agents/admins');
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
                ->nullable(),
        ];
    }
}
