<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ticket;
use OpenApi\Attributes as OA;

class TicketController extends Controller
{
    #[OA\Get(
        path: '/api/tickets',
        summary: 'List all tickets',
        tags: ['Tickets'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'List of tickets',
                content: new OA\JsonContent(type: 'array', items: new OA\Items(
                    properties: [
                        new OA\Property(property: 'id', type: 'integer'),
                        new OA\Property(property: 'title', type: 'string'),
                        new OA\Property(property: 'description', type: 'string'),
                        new OA\Property(property: 'status', type: 'string', enum: ['open', 'in_progress', 'resolved', 'closed']),
                        new OA\Property(property: 'priority', type: 'string', enum: ['low', 'medium', 'high', 'urgent']),
                    ]
                ))
            )
        ]
    )]
    public function index()
    {
        return response()->json(
            Ticket::with(['category', 'creator', 'assignee'])->get()
        );
    }

    #[OA\Post(
        path: '/api/tickets',
        summary: 'Create a new ticket',
        tags: ['Tickets'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['title', 'priority', 'created_by'],
                properties: [
                    new OA\Property(property: 'title', type: 'string', example: 'My ticket'),
                    new OA\Property(property: 'description', type: 'string', example: 'Ticket description'),
                    new OA\Property(property: 'status', type: 'string', enum: ['open', 'in_progress', 'resolved', 'closed']),
                    new OA\Property(property: 'priority', type: 'string', enum: ['low', 'medium', 'high', 'urgent']),
                    new OA\Property(property: 'category_id', type: 'integer', example: 1),
                    new OA\Property(property: 'created_by', type: 'integer', example: 1),
                    new OA\Property(property: 'assigned_to', type: 'integer', example: 1),
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
        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'status'      => 'sometimes|in:open,in_progress,resolved,closed',
            'priority'    => 'required|in:low,medium,high,urgent',
            'category_id' => 'nullable|exists:categories,id',
            'assigned_to' => 'nullable|exists:users,id',
            'created_by'  => 'required|exists:users,id',
        ]);

        $ticket = Ticket::create([
            'title'       => $validated['title'],
            'description' => $validated['description'] ?? null,
            'status'      => $validated['status'] ?? 'open',
            'priority'    => $validated['priority'],
            'category_id' => $validated['category_id'] ?? null,
            'assigned_to' => $validated['assigned_to'] ?? null,
            'created_by'  => $request->user()->id,
        ]);

        return response()->json($ticket->load(['category', 'creator', 'assignee']), 201);
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
        return response()->json($ticket->load(['category', 'creator', 'assignee']));
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
                    new OA\Property(property: 'status', type: 'string', enum: ['open', 'in_progress', 'resolved', 'closed']),
                    new OA\Property(property: 'priority', type: 'string', enum: ['low', 'medium', 'high', 'urgent']),
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
        $validated = $request->validate([
            'title'       => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'status'      => 'sometimes|in:open,in_progress,resolved,closed',
            'priority'    => 'sometimes|in:low,medium,high,urgent',
            'category_id' => 'nullable|exists:categories,id',
            'assigned_to' => 'nullable|exists:users,id',
            'created_by'  => 'sometimes|exists:users,id',
        ]);

        $ticket->update($validated);

        return response()->json(
            $ticket->load(['category', 'creator', 'assignee'])
        );
    }

    #[OA\Delete(
        path: '/api/tickets/{ticket}',
        summary: 'Delete a ticket',
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
        $ticket->delete();

        return response()->json([
            'message' => 'Ticket deleted'
        ]);
    }
}
