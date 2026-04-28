<?php

namespace App\Mcp\Tools;

use App\Models\Comment;
use App\Http\Resources\CommentResource;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Tool;

#[Description('Update a comment (only owner and if ticket is not in final state).')]
class UpdateComment extends Tool
{
    public function handle(Request $request): Response
    {
        $user = $request->user();

        if (!$user) {
            return Response::error('Unauthorized.');
        }

        $data = $request->validate([
            'comment_id' => 'required|exists:comments,id',
            'comment'    => 'sometimes|string',
        ]);

        $comment = Comment::with('ticket')->findOrFail($data['comment_id']);

        if ($comment->ticket->isFinalState()) {
            return Response::error('Cannot update comment on a closed ticket.');
        }

        if ($comment->user_id !== $user->id) {
            return Response::error('You can only update your own comments.');
        }

        $comment->update([
            'comment' => $data['comment'] ?? $comment->comment,
        ]);

        return Response::json([
            'success' => true,
            'comment' => new CommentResource($comment->load(['ticket', 'user'])),
        ]);
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'comment_id' => $schema->integer()
                ->description('ID of the comment to update')
                ->required(),
            'comment' => $schema->string()
                ->description('Updated comment content')
                ->nullable(),
        ];
    }
}
