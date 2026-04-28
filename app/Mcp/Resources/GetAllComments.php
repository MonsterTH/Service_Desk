<?php

namespace App\Mcp\Resources;

use App\Models\Comment;
use App\Models\Ticket;
use App\Http\Resources\CommentResource;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Resource;

#[Description('Get all comments for a specific ticket (filtered by role).')]
class GetAllComments extends Resource
{
    public function handle(Request $request): Response
    {
        /** @var \App\Models\User $user */
        $user = $request->user();

        if (!$user) {
            return Response::error('Unauthorized.');
        }

        $data = $request->validate([
            'ticket_id'    => 'required|exists:tickets,id',
            'page'         => 'nullable|integer|min:1',
            'ItemsPerPage' => 'nullable|integer|min:1',
        ]);

        $ticket = Ticket::findOrFail($data['ticket_id']);

        $canView = $user->hasRole('admin')
            || ($user->hasRole('agent') && $ticket->assigned_to === $user->id)
            || ($user->hasRole('employee') && $ticket->created_by === $user->id);

        if (!$canView) {
            return Response::error('You do not have permission to view comments on this ticket.');
        }

        $query = Comment::with(['ticket', 'user'])
            ->where('ticket_id', $ticket->id);

        if ($user->hasRole('employee')) {
            $query->where('is_internal', false);
        }

        $paginator = $query->paginate(
            $data['ItemsPerPage'] ?? 5,
            ['*'],
            'page',
            $data['page'] ?? 1
        );

        return Response::json([
            'data' => CommentResource::collection($paginator),
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'total'        => $paginator->total(),
                'message'      => $paginator->count() === 0 ? 'No results for this page' : null,
            ],
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
