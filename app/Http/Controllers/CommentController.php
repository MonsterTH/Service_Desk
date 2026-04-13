<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Comment;

class CommentController extends Controller
{
    /**
     * GET /comments
     */
    public function index()
    {
        return Comment::with(['ticket', 'user'])->get();
    }

    /**
     * POST /comments
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'ticket_id' => 'required|exists:tickets,id',
            'comment' => 'required|string',
            'is_internal' => 'boolean',
        ]);

        $comment = Comment::create([
            'ticket_id' => $validated['ticket_id'],
            'user_id' => $request->user()->id,
            'comment' => $validated['comment'],
            'is_internal' => $validated['is_internal'] ?? false,
        ]);

        return response()->json($comment, 201);
    }

    /**
     * GET /comments/{id}
     */
    public function show(string $id)
    {
        $comment = Comment::with(['ticket', 'user'])->findOrFail($id);

        return response()->json($comment);
    }

    /**
     * PUT /comments/{id}
     */
    public function update(Request $request, string $id)
    {
        $comment = Comment::findOrFail($id);

        // só autor pode editar (regra simples)
        if ($comment->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $validated = $request->validate([
            'comment' => 'sometimes|string',
            'is_internal' => 'boolean',
        ]);

        $comment->update($validated);

        return response()->json($comment);
    }

    /**
     * DELETE /comments/{id}
     */
    public function destroy(Request $request, string $id)
    {
        $comment = Comment::findOrFail($id);

        // admin ou dono do comentário
        if (
            $request->user()->role !== 'admin' &&
            $comment->user_id !== $request->user()->id
        ) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $comment->delete();

        return response()->json(['message' => 'Comment deleted']);
    }
}
