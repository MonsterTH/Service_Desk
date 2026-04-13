<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ticket;

class TicketController extends Controller
{
    /**
     * GET /tickets
     */
    public function index(Request $request)
    {
        $user = $request->user();

        if ($user->role === 'admin') {
            return Ticket::with(['category', 'creator', 'assignee'])->get();
        }

        if ($user->role === 'agent') {
            return Ticket::with(['category', 'creator', 'assignee'])
                ->where('assigned_to', $user->id)
                ->get();
        }

        return Ticket::with(['category', 'creator', 'assignee'])
            ->where('created_by', $user->id)
            ->get();
    }

    /**
     * POST /tickets
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'priority' => 'required|in:low,medium,high,urgent',
            'category_id' => 'nullable|exists:categories,id',
            'assigned_to' => 'nullable|exists:users,id',
        ]);

        $ticket = Ticket::create([
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'status' => 'open',
            'priority' => $validated['priority'],
            'category_id' => $validated['category_id'] ?? null,
            'assigned_to' => $validated['assigned_to'] ?? null,
            'created_by' => $request->user()->id,
        ]);

        return response()->json($ticket, 201);
    }

    /**
     * GET /tickets/{id}
     */
    public function show(Request $request, string $id)
    {
        $ticket = Ticket::with(['category', 'creator', 'assignee'])
            ->findOrFail($id);

        $user = $request->user();

        // regras de acesso
        if ($user->role === 'employee' && $ticket->created_by !== $user->id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        if ($user->role === 'agent' && $ticket->assigned_to !== $user->id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        return response()->json($ticket);
    }

    /**
     * PUT /tickets/{id}
     */
    public function update(Request $request, string $id)
    {
        $ticket = Ticket::findOrFail($id);
        $user = $request->user();

        // regras simples
        if (!in_array($user->role, ['admin', 'agent'])) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'status' => 'sometimes|in:open,in_progress,resolved,closed',
            'priority' => 'sometimes|in:low,medium,high,urgent',
            'category_id' => 'nullable|exists:categories,id',
            'assigned_to' => 'nullable|exists:users,id',
        ]);

        $ticket->update($validated);

        return response()->json($ticket);
    }

    /**
     * DELETE /tickets/{id}
     */
    public function destroy(Request $request, string $id)
    {
        $ticket = Ticket::findOrFail($id);
        $user = $request->user();

        if ($user->role !== 'admin') {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $ticket->delete();

        return response()->json(['message' => 'Ticket deleted']);
    }
}
