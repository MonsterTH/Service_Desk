<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ticket;
use App\Models\User;
use OpenApi\Attributes as OA;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
class TicketController extends Controller
{
    use AuthorizesRequests;

    #[OA\Get(
        path: '/api/tickets',
        summary: 'List all tickets',
        tags: ['Tickets'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'List of tickets',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: 'data',
                            type: 'array',
                            items: new OA\Items(
                                type: 'object',
                                properties: [
                                    new OA\Property(property: 'id', type: 'integer'),
                                    new OA\Property(property: 'title', type: 'string'),
                                    new OA\Property(property: 'description', type: 'string'),
                                    new OA\Property(property: 'status', type: 'string'),
                                    new OA\Property(property: 'priority', type: 'string'),
                                ]
                            )
                        )
                    ]
                )
            )
        ]
    )]
    public function index(Request $request)
    {
        $this->authorize('viewAny', Ticket::class);

        $user = $request->user();

        $query = Ticket::with(['category', 'creator', 'assignee'])
            ->whereNotIn('status', ['resolved', 'closed']);

        if ($user->hasRole('admin')) {
            return response()->json($query->latest()->paginate(20));
        }

        if ($user->hasRole('agent')) {
            $query->where('assigned_to', $user->id)
                ->whereIn('status', ['open', 'in_progress']);
        }

        if ($user->hasRole('employee')) {
            $query->where('created_by', $user->id);
        }

        return response()->json($query->latest()->paginate(20));
    }

    #[OA\Post(
        path: '/api/tickets',
        summary: 'Create a new ticket',
        tags: ['Tickets'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['title', 'priority'],
                properties: [
                    new OA\Property(property: 'title', type: 'string', example: 'My ticket'),
                    new OA\Property(property: 'description', type: 'string'),
                    new OA\Property(property: 'category_id', type: 'integer', default: 1),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: 'Ticket created'),
            new OA\Response(response: 422, description: 'Validation error'),
        ]
    )]
    public function store(Request $request)
    {
        $this->authorize('create', Ticket::class);

        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'category_id' => 'nullable|exists:categories,id',
        ]);

        $ticket = Ticket::create([
            'title'       => $validated['title'],
            'description' => $validated['description'] ?? null,
            'status'      => 'open',
            'priority'    => 'low',
            'category_id' => $validated['category_id'] ?? null,
            'assigned_to' => null,
            'created_by'  => $request->user()->id,
        ]);

        return response()->json($ticket, 201);
    }

    #[OA\Get(
        path: '/api/tickets/{ticket}',
        summary: 'Get a ticket by ID',
        tags: ['Tickets'],
        parameters: [
            new OA\Parameter(name: 'ticket', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(response: 200, description: 'Ticket found'),
            new OA\Response(response: 404, description: 'Ticket not found'),
        ]
    )]
    public function show(Ticket $ticket)
    {
        $this->authorize('view', $ticket);

        return response()->json($ticket);
    }

    #[OA\Put(
        path: '/api/tickets/{ticket}',
        summary: 'Update a ticket',
        tags: ['Tickets'],
        parameters: [
            new OA\Parameter(name: 'ticket', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))
        ],
        requestBody: new OA\RequestBody(
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'title', type: 'string'),
                    new OA\Property(property: 'description', type: 'string'),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Ticket updated'),
            new OA\Response(response: 404, description: 'Ticket not found'),
        ]
    )]
    public function update(Request $request, Ticket $ticket)
    {
        $this->authorize('update', $ticket);

        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
        ]);

        $ticket->update($validated);

        return response()->json($ticket);
    }

    #[OA\Delete(
        path: '/api/tickets/{ticket}',
        summary: 'Delete a ticket (Admin only)',
        tags: ['Tickets'],
        parameters: [
            new OA\Parameter(name: 'ticket', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(response: 200, description: 'Ticket deleted'),
            new OA\Response(response: 404, description: 'Ticket not found'),
        ]
    )]
    public function destroy(Ticket $ticket)
    {
        $this->authorize('delete', $ticket);

        $ticket->delete();

        return response()->json([
            'message' => 'Ticket deleted'
        ]);
    }

    #[OA\Patch(
        path: '/api/tickets/{ticket}/assign',
        summary: 'Assign a ticket to a user (Agent/Admin only)',
        tags: ['Tickets'],
        parameters: [
            new OA\Parameter(
                name: 'ticket',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer')
            )
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['assigned_to'],
                properties: [
                    new OA\Property(
                        property: 'assigned_to',
                        type: 'integer',
                        example: 2
                    )
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Ticket assigned successfully'),
            new OA\Response(response: 403, description: 'Forbidden'),
            new OA\Response(response: 422, description: 'Validation error'),
        ]
    )]

    public function assign(Request $request, Ticket $ticket)
    {
        $validated = $request->validate([
            'assigned_to' => 'required|exists:users,id',
        ]);

        $targetUser = User::findOrFail($validated['assigned_to']);

        $this->authorize('assign', [$ticket, $targetUser]);

        $ticket->update([
            'assigned_to' => $targetUser->id,
        ]);

        return response()->json($ticket);
    }

    #[OA\Patch(
        path: '/api/tickets/{ticket}/status',
        summary: 'Update ticket status (Agent/Admin only)',
        tags: ['Tickets'],
        parameters: [
            new OA\Parameter(
                name: 'ticket',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer')
            )
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['status'],
                properties: [
                    new OA\Property(
                        property: 'status',
                        type: 'string',
                        enum: ['open', 'in_progress', 'resolved', 'closed']
                    )
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Status updated successfully'),
            new OA\Response(response: 403, description: 'Forbidden'),
            new OA\Response(response: 422, description: 'Validation error'),
        ]
    )]

    public function updateStatus(Request $request, Ticket $ticket)
    {
        $this->authorize('updateStatus', $ticket);

        $validated = $request->validate([
            'status' => 'required|in:open,in_progress,resolved,closed',
        ]);

        $ticket->update([
            'status' => $validated['status'],
        ]);

        return response()->json($ticket);
    }

    #[OA\Patch(
        path: '/api/tickets/{ticket}/priority',
        summary: 'Update ticket priority (Agent/Admin only)',
        tags: ['Tickets'],
        parameters: [
            new OA\Parameter(
                name: 'ticket',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer')
            )
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['priority'],
                properties: [
                    new OA\Property(
                        property: 'priority',
                        type: 'string',
                        enum: ['low', 'medium', 'high', 'urgent']
                    )
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Priority updated'),
            new OA\Response(response: 403, description: 'Forbidden'),
            new OA\Response(response: 422, description: 'Validation error'),
        ]
    )]

    public function updatePriority(Request $request, Ticket $ticket)
    {
        // chama a policy
        $this->authorize('updatePriority', $ticket);

        $validated = $request->validate([
            'priority' => 'required|in:low,medium,high,urgent',
        ]);

        $ticket->update([
            'priority' => $validated['priority'],
        ]);

        return response()->json([
            'message' => 'Priority updated successfully',
            'ticket' => $ticket
        ]);
    }
}
