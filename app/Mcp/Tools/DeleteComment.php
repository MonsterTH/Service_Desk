<?php

namespace App\Mcp\Tools;

use App\Models\Comment;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Tool;
use Laravel\Mcp\Server\Tools\Annotations\IsDestructive;

#[Description('Delete a comment (admin or owner only).')]
#[IsDestructive]
class DeleteComment extends Tool
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
            'comment_id' => 'required|exists:comments,id',
            'confirm'    => 'required|boolean',
        ]);

        if (! $data['confirm']) {
            return Response::error('You must confirm before deleting this comment.');
        }

        $comment = Comment::findOrFail($data['comment_id']);

        // Policy
        $this->authorize('delete', $comment);

        $comment->delete();

        return Response::json([
            'message' => 'Comment deleted successfully'
        ]);
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'comment_id' => $schema->integer()
                ->description('The ID of the comment to delete')
                ->required(),

            'confirm' => $schema->boolean()
                ->description('Must be TRUE only if the user explicitly confirmed deletion')
                ->required(),
        ];
    }
}
