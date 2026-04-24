<?php

namespace App\Mcp\Resources;

use Illuminate\Http\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Resource;
use App\Models\Ticket;
use App\QueryFilters\TicketFilter;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

#[Description('Retrieve a list of tickets with optional filters such as status, priority, category, or assigned user.')]
class GetAllTickets extends Resource
{
    use AuthorizesRequests;

    public function handle(Request $request): Response
    {
        $user = $request->user();

        if (! $user) {
            return Response::error('Unauthorized');
        }

        $this->authorize('viewAny', Ticket::class);

        $query = Ticket::with(['category', 'creator', 'assignee']);

        // aplicar filtros (igual ao controller)
        $filter = app(TicketFilter::class);
        $query = $filter->apply($query, $request);

        // regras por role
        if ($user->hasRole('agent')) {
            $query->where('assigned_to', $user->id);
        }

        if ($user->hasRole('employee')) {
            $query->where('created_by', $user->id);
        }

        $perPage = $request->input('ItemsPerPage', 5);

        $paginator = $query->paginate($perPage);

        return Response::json([
            'data' => $paginator->items(),
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
            'status' => $schema->string()
                ->description('Filter by ticket status (open, in_progress, resolved, closed)')
                ->nullable(),

            'priority' => $schema->string()
                ->description('Filter by priority (low, medium, high, urgent)')
                ->nullable(),

            'category_id' => $schema->integer()
                ->description('Filter by category ID')
                ->nullable(),

            'assigned_to' => $schema->integer()
                ->description('Filter by assigned user ID')
                ->nullable(),

            'search' => $schema->string()
                ->description('Search in ticket title or description')
                ->nullable(),

            'page' => $schema->integer()
                ->description('Page number')
                ->nullable(),

            'ItemsPerPage' => $schema->integer()
                ->description('Number of items per page')
                ->nullable(),
        ];
    }
}
