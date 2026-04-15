<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Comment;
use OpenApi\Attributes as OA;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Models\Ticket;

class CommentController extends Controller
{
    use AuthorizesRequests;

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
    public function index(Request $request)
    {
        $this->authorize('viewAny', Comment::class);

        $user = $request->user();

        $query = Comment::with(['ticket', 'user']);

        if ($user->hasRole('admin')) {
            return response()->json($query->get());
        }

        $query->where('user_id', $user->id);

        return response()->json($query->get());
    }

    #[OA\Post(
        path: '/api/comments',
        summary: 'Create a new comment',
        tags: ['Comments'],
        parameters: [
            new OA\Parameter(name: 'ticket_id', in: 'query', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['ticket_id', 'comment'],
                properties: [
                    new OA\Property(property: 'comment', type: 'string', example: 'Comment content'),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: 'Comment created'),
            new OA\Response(response: 403, description: 'Forbidden'),
            new OA\Response(response: 422, description: 'Validation error'),
        ]
    )]
    public function store(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'ticket_id'   => 'required|exists:tickets,id',
            'comment'     => 'required|string',
            'is_internal' => 'boolean',
        ]);

        $ticket = Ticket::findOrFail($validated['ticket_id']);

        $this->authorize('create', [Comment::class, $ticket]);

        $comment = Comment::create([
            'ticket_id'   => $validated['ticket_id'],
            'user_id'     => $user->id,
            'comment'     => $validated['comment'],
            'is_internal' => false,
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
        $this->authorize('view', $comment);
        return response()->json($comment->load(['ticket', 'user']), 200);
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
        $this->authorize('update', $comment);

        $validated = $request->validate([
            'comment'     => 'sometimes|string',
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
        $this->authorize('delete', $comment);

        $comment->delete();

        return response()->json([
            'message' => 'Comment deleted'
        ]);
    }

    #[OA\Patch(
        path: '/api/tickets/{ticket}/internal-comments',
        summary: 'Create a new Internal comment (Admin, Agent only)',
        tags: ['Comments'],
        parameters: [
            new OA\Parameter(name: 'ticket', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['comment'],
                properties: [
                    new OA\Property(property: 'comment', type: 'string', example: 'Comment content'),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: 'Comment created'),
            new OA\Response(response: 403, description: 'Forbidden'),
            new OA\Response(response: 422, description: 'Validation error'),
        ]
    )]

    public function internal_comments(Request $request, Ticket $ticket)
    {
        $user = $request->user();

        $this->authorize('internal_comments', $ticket);

        $validated = $request->validate([
            'comment' => 'required|string',
        ]);

        $comment = Comment::create([
            'ticket_id'   => $ticket->id,
            'user_id'     => $user->id,
            'comment'     => $validated['comment'],
            'is_internal' => true,
        ]);

        return response()->json($comment, 201);
    }
}
