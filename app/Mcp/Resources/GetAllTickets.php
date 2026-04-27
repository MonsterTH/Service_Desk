<?php

namespace App\Mcp\Resources;

use Illuminate\Http\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use App\Models\Ticket;
use App\QueryFilters\TicketFilter;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Http\Resources\TicketResource;
use Laravel\Mcp\Server\Resource;

#[Description('Retrieve a list of tickets with optional filters such as status, priority, category, or assigned user.')]
class GetAllTickets extends Resource
{
    use AuthorizesRequests;

    public function handle(Request $request): Response
    {
        /** @var \App\Models\User $user */
        $user = $request->user();

        if (! $user) {
            return Response::error('Unauthorized');
        }

        $this->authorize('viewAny', Ticket::class);

        $query = Ticket::with(['category', 'creator', 'assignee']);

        $filter = app(TicketFilter::class);
        $filters = $request->all();

        $query = $filter->apply($query, $filters);

        if ($user->hasRole('agent')) {
            $query->where('assigned_to', $user->id);
        }

        if ($user->hasRole('employee')) {
            $query->where('created_by', $user->id);
        }

        $perPage = $filters['ItemsPerPage'] ?? 5;

        $paginator = $query->paginate($perPage);

        return Response::json([
            'data' => TicketResource::collection($paginator),
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'total' => $paginator->total(),
                'per_page' => $paginator->perPage(),
            ],
        ]);
    }

    public function schema(\Illuminate\Contracts\JsonSchema\JsonSchema $schema): array
    {
        return [
            'status' => $schema->string()->nullable(),
            'priority' => $schema->string()->nullable(),
            'category_id' => $schema->integer()->nullable(),
            'assigned_to' => $schema->integer()->nullable(),
            'search' => $schema->string()->nullable(),
            'created_from' => $schema->string()->nullable(),
            'created_to' => $schema->string()->nullable(),
            'reopened' => $schema->boolean()->nullable(),
            'page' => $schema->integer()->nullable(),
            'ItemsPerPage' => $schema->integer()->nullable(),
        ];
    }
}
