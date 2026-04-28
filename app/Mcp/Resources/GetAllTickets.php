<?php

namespace App\Mcp\Resources;

use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Resource;
use App\Models\Ticket;
use App\Http\Resources\TicketResource;

#[Description('Retrieve a list of tickets with optional filters.')]
class GetAllTickets extends Resource
{
    public function handle(Request $request): Response
    {
        /** @var \App\Models\User $user */
        $user = $request->user();

        if (!$user) {
            return Response::error('Unauthorized');
        }

        $query = Ticket::with(['category', 'creator', 'assignee']);

        if ($status = $request->get('status')) {
            $query->where('status', $status);
        }
        if ($priority = $request->get('priority')) {
            $query->where('priority', $priority);
        }
        if ($categoryId = $request->get('category_id')) {
            $query->where('category_id', $categoryId);
        }
        if ($assignedTo = $request->get('assigned_to')) {
            $query->where('assigned_to', $assignedTo);
        }
        if ($search = $request->get('search')) {
            $query->where('title', 'like', "%{$search}%");
        }

        if ($user->hasRole('agent')) {
            $query->where('assigned_to', $user->id);
        }
        if ($user->hasRole('employee')) {
            $query->where('created_by', $user->id);
        }

        $perPage = $request->get('ItemsPerPage', 5);
        $paginator = $query->paginate($perPage);

        return Response::json([
            'data' => TicketResource::collection($paginator),
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'total'        => $paginator->total(),
                'per_page'     => $paginator->perPage(),
            ],
        ]);
    }

    public function schema(\Illuminate\Contracts\JsonSchema\JsonSchema $schema): array
    {
        return [
            'status'       => $schema->string()->nullable(),
            'priority'     => $schema->string()->nullable(),
            'category_id'  => $schema->integer()->nullable(),
            'assigned_to'  => $schema->integer()->nullable(),
            'search'       => $schema->string()->nullable(),
            'created_from' => $schema->string()->nullable(),
            'created_to'   => $schema->string()->nullable(),
            'reopened'     => $schema->boolean()->nullable(),
            'page'         => $schema->integer()->nullable(),
            'ItemsPerPage' => $schema->integer()->nullable(),
        ];
    }
}
