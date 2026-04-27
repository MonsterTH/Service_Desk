<?php

namespace App\Mcp\Tools;

use App\Models\Comment;
use App\Http\Resources\CommentResource;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Tool;

#[Description('Get a specific comment by ID (respecting permissions and visibility rules).')]
class GetComment extends Tool
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
        ]);

        $comment = Comment::with(['ticket', 'user'])
            ->findOrFail($data['comment_id']);

        // Policy
        $this->authorize('view', $comment);

        // regra extra: employee não pode ver comentários internos
        if ($comment->is_internal && $user->hasRole('employee')) {
            return Response::error('Forbidden: internal comment');
        }

        return Response::json([
            'data' => new CommentResource($comment)
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
