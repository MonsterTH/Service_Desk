<?php

namespace App\Mcp\Resources;

use App\Models\Comment;
use App\Http\Resources\CommentResource;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Resource;

#[Description('Get a specific comment by ID.')]
class GetComment extends Resource
{
    public function handle(Request $request): Response
    {
        /** @var \App\Models\User $user */
        $user = $request->user();

        if (!$user) {
            return Response::error('Unauthorized.');
        }

        $data = $request->validate([
            'comment_id' => 'required|exists:comments,id',
        ]);

        $comment = Comment::with(['ticket', 'user'])
            ->findOrFail($data['comment_id']);

        $canView = $user->hasRole('admin')
            || $comment->user_id === $user->id
            || ($user->hasRole('agent') && $comment->ticket->assigned_to === $user->id);

        if (!$canView) {
            return Response::error('You do not have permission to view this comment.');
        }

        if ($comment->is_internal && $user->hasRole('employee')) {
            return Response::error('Forbidden: internal comment.');
        }

        return Response::json([
            'data' => new CommentResource($comment),
        ]);
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'comment_id' => $schema->integer()
                ->description('ID of the comment')
                ->required(),
        ];
    }
}
