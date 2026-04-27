<?php

namespace App\Mcp\Resources;

use App\Models\Comment;
use App\Models\Ticket;
use App\Http\Resources\CommentResource;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Resource;

#[Description('Get all comments for a specific ticket (filtered by role).')]
class GetAllComments extends Resource
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
            'ticket_id'    => 'required|exists:tickets,id',
            'page'         => 'nullable|integer|min:1',
            'ItemsPerPage' => 'nullable|integer|min:1',
        ]);

        $ticket = Ticket::findOrFail($data['ticket_id']);

        // Policy
        $this->authorize('viewAny', [Comment::class, $ticket]);

        $query = Comment::with(['ticket', 'user'])
            ->where('ticket_id', $ticket->id);

        // regra: employee NÃO vê internos
        if ($user->hasRole('employee')) {
            $query->where('is_internal', false);
        }

        $perPage = $data['ItemsPerPage'] ?? 5;
        $page = $data['page'] ?? 1;

        $paginator = $query->paginate($perPage, ['*'], 'page', $page);

        return Response::json([
            'data' => CommentResource::collection($paginator),
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'total' => $paginator->total(),
                'message' => $paginator->count() === 0
                    ? 'No results for this page'
                    : null,
            ]
        ]);
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'ticket_id' => $schema->integer()
                ->description('Ticket ID to fetch comments from')
                ->required(),

            'page' => $schema->integer()
                ->description('Page number')
                ->nullable(),

            'ItemsPerPage' => $schema->integer()
                ->description('Number of comments per page')
                ->nullable(),
        ];
    }
}
