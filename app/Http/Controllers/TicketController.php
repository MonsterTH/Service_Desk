<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ticket;

class TicketController extends Controller
{
    /**
     * GET /tickets
     */
    public function index()
    {
        return response()->json(
            Ticket::with(['category', 'creator', 'assignee'])->get()
        );
    }

    /**
     * POST /tickets
     */
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
            'created_by'  => $validated['created_by'],
        ]);

        return response()->json($ticket->load(['category', 'creator', 'assignee']), 201);
    }

    /**
     * GET /tickets/{ticket}
     */
    public function show(string $id)
    {
        $ticket = Ticket::with(['category', 'creator', 'assignee'])->findOrFail($id);

        return response()->json($ticket);
    }

    /**
     * PUT /tickets/{ticket}
     */
    public function update(Request $request, string $id)
    {
        $ticket = Ticket::findOrFail($id);

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

        return response()->json($ticket->load(['category', 'creator', 'assignee']));
    }

    /**
     * DELETE /tickets/{ticket}
     */
    public function destroy(string $id)
    {
        $ticket = Ticket::findOrFail($id);
        $ticket->delete();

        return response()->json(['message' => 'Ticket deleted']);
    }
}