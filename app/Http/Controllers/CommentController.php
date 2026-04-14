<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Comment;
use OpenApi\Attributes as OA;

class CommentController extends Controller
{
    #[OA\Get(
        path: '/api/comments',
        summary: 'List all comments',
        tags: ['Comments'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'List of comments',
                content: new OA\JsonContent(type: 'array', items: new OA\Items(
                    properties: [
                        new OA\Property(property: 'id', type: 'integer'),
                        new OA\Property(property: 'ticket_id', type: 'integer'),
                        new OA\Property(property: 'user_id', type: 'integer'),
                        new OA\Property(property: 'comment', type: 'string'),
                        new OA\Property(property: 'is_internal', type: 'boolean'),
                    ]
                ))
            )
        ]
    )]
    public function index()
    {
        return response()->json(
            Comment::with(['ticket', 'user'])->get()
        );
    }

    #[OA\Post(
        path: '/api/comments',
        summary: 'Create a new comment',
        tags: ['Comments'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['ticket_id', 'user_id', 'comment'],
                properties: [
                    new OA\Property(property: 'ticket_id', type: 'integer', example: 1),
                    new OA\Property(property: 'user_id', type: 'integer', example: 1),
                    new OA\Property(property: 'comment', type: 'string', example: 'Comment content'),
                    new OA\Property(property: 'is_internal', type: 'boolean', example: false),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: 'Comment created'),
            new OA\Response(response: 422, description: 'Validation error'),
        ]
    )]
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

    #[OA\Get(
        path: '/api/comments/{comment}',
        summary: 'Get a comment by ID',
        tags: ['Comments'],
        parameters: [
            new OA\Parameter(name: 'comment', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(response: 200, description: 'Comment found'),
            new OA\Response(response: 404, description: 'Comment not found'),
        ]
    )]

    public function show(Comment $comment)
    {
        return response()->json($comment->load(['ticket', 'user']));
    }

    #[OA\Put(
        path: '/api/comments/{comment}',
        summary: 'Update a comment',
        tags: ['Comments'],
        parameters: [
            new OA\Parameter(name: 'comment', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))
        ],
        requestBody: new OA\RequestBody(
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'comment', type: 'string'),
                    new OA\Property(property: 'is_internal', type: 'boolean'),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Comment updated'),
            new OA\Response(response: 404, description: 'Comment not found'),
        ]
    )]
    public function update(Request $request, Comment $comment)
    {
        $validated = $request->validate([
            'comment'     => 'sometimes|string',
            'is_internal' => 'boolean',
        ]);

        $comment->update($validated);

        return response()->json($comment->load(['ticket', 'user']));
    }

    #[OA\Delete(
        path: '/api/comments/{comment}',
        summary: 'Delete a comment',
        tags: ['Comments'],
        parameters: [
            new OA\Parameter(name: 'comment', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(response: 200, description: 'Comment deleted'),
            new OA\Response(response: 404, description: 'Comment not found'),
        ]
    )]
    public function destroy(Comment $comment)
    {
        $comment->delete();

        return response()->json([
            'message' => 'Comment deleted'
        ]);
    }
}
