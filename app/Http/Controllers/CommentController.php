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
        return response()->json(
            Comment::with(['ticket', 'user'])->get()
        );
    }

    /**
     * POST /comments
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'ticket_id'   => 'required|exists:tickets,id',
            'user_id'     => 'required|exists:users,id',
            'comment'     => 'required|string',
            'is_internal' => 'boolean',
        ]);

        $comment = Comment::create([
            'ticket_id'   => $validated['ticket_id'],
            'user_id'     => $validated['user_id'],
            'comment'     => $validated['comment'],
            'is_internal' => $validated['is_internal'] ?? false,
        ]);

        return response()->json($comment->load(['ticket', 'user']), 201);
    }

    /**
     * GET /comments/{comment}
     */
    public function show(string $id)
    {
        $comment = Comment::with(['ticket', 'user'])->findOrFail($id);

        return response()->json($comment);
    }

    /**
     * PUT /comments/{comment}
     */
    public function update(Request $request, string $id)
    {
        $comment = Comment::findOrFail($id);

        $validated = $request->validate([
            'comment'     => 'sometimes|string',
            'is_internal' => 'boolean',
        ]);

        $comment->update($validated);

        return response()->json($comment->load(['ticket', 'user']));
    }

    /**
     * DELETE /comments/{comment}
     */
    public function destroy(string $id)
    {
        $comment = Comment::findOrFail($id);
        $comment->delete();

        return response()->json(['message' => 'Comment deleted']);
    }
}