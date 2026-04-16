<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Comment;
use App\Models\Ticket;
use App\Http\Resources\CommentResource;
use OpenApi\Attributes as OA;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class CommentController extends Controller
{
    use AuthorizesRequests;

    #[OA\Get(
        path: '/api/tickets/{ticket}/comments',
        summary: 'List all comments of a ticket',
        tags: ['Comments'],
        parameters: [
            new OA\Parameter(
                name: 'ticket',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer')
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'List of comments',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: 'data',
                            type: 'array',
                            items: new OA\Items(ref: '#/components/schemas/Comment')
                        )
                    ]
                )
            ),
            new OA\Response(response: 401, description: 'Unauthorized'),
        ]
    )]
    public function index(Request $request, Ticket $ticket)
    {
        $this->authorize('viewAny', [Comment::class, $ticket]);

        $query = Comment::with(['ticket', 'user'])
            ->where('ticket_id', $ticket->id);

        if ($request->user()->hasRole('employee')) {
            $query->where('is_internal', false);
        }

        return CommentResource::collection($query->get());
    }

    #[OA\Post(
        path: '/api/tickets/{ticket}/comments',
        summary: 'Create a comment for a ticket',
        tags: ['Comments'],
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
                required: ['comment'],
                properties: [
                    new OA\Property(property: 'comment', type: 'string', example: 'Comment content'),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: 'Comment created',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: 'data',
                            ref: '#/components/schemas/Comment'
                        )
                    ]
                )
            ),
            new OA\Response(response: 401, description: 'Unauthorized'),
            new OA\Response(response: 403, description: 'Forbidden'),
            new OA\Response(response: 422, description: 'Validation error'),
        ]
    )]
    public function store(Request $request, Ticket $ticket)
    {
        $user = $request->user();

        $validated = $request->validate([
            'comment'     => 'required|string',
        ]);

        $this->authorize('create', [Comment::class, $ticket]);

        $comment = Comment::create([
            'ticket_id'   => $ticket->id,
            'user_id'     => $user->id,
            'comment'     => $validated['comment'],
            'is_internal' => false,
        ]);

        return new CommentResource($comment->load(['ticket', 'user']));
    }

    #[OA\Get(
        path: '/api/tickets/{ticket}/comments/{comment}',
        summary: 'Get a comment by ID',
        tags: ['Comments'],
        parameters: [
            new OA\Parameter(
                name: 'ticket',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer')
            ),
            new OA\Parameter(
                name: 'comment',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer')
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Comment found',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: 'data',
                            ref: '#/components/schemas/Comment'
                        )
                    ]
                )
            ),
            new OA\Response(response: 401, description: 'Unauthorized'),
            new OA\Response(response: 404, description: 'Comment not found'),
        ]
    )]
    public function show(Ticket $ticket, Comment $comment)
    {
        $this->authorize('view', $comment);

        abort_if($comment->ticket_id !== $ticket->id, 404);

        return new CommentResource($comment->load(['ticket', 'user']));
    }

    #[OA\Put(
        path: '/api/tickets/{ticket}/comments/{comment}',
        summary: 'Update a comment',
        tags: ['Comments'],
        parameters: [
            new OA\Parameter(
                name: 'ticket',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer')
            ),
            new OA\Parameter(
                name: 'comment',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer')
            )
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(
                        property: 'comment',
                        type: 'string',
                        example: 'Updated comment'
                    ),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Comment updated',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: 'data',
                            ref: '#/components/schemas/Comment'
                        )
                    ]
                )
            ),
            new OA\Response(response: 401, description: 'Unauthorized'),
            new OA\Response(response: 404, description: 'Comment not found'),
        ]
    )]
    public function update(Request $request, Ticket $ticket, Comment $comment)
    {
        $this->authorize('update', $comment);

        abort_if($comment->ticket_id !== $ticket->id, 404);

        $validated = $request->validate([
            'comment' => 'sometimes|string',
        ]);

        $comment->update($validated);

        return new CommentResource($comment->load(['ticket', 'user']));
    }

    #[OA\Delete(
        path: '/api/tickets/{ticket}/comments/{comment}',
        summary: 'Delete a comment',
        tags: ['Comments'],
        parameters: [
            new OA\Parameter(
                name: 'ticket',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer')
            ),
            new OA\Parameter(
                name: 'comment',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer')
            )
        ],
        responses: [
            new OA\Response(response: 200, description: 'Comment deleted'),
            new OA\Response(response: 401, description: 'Unauthorized'),
            new OA\Response(response: 404, description: 'Comment not found'),
        ]
    )]
    public function destroy(Ticket $ticket, Comment $comment)
    {
        $this->authorize('delete', $comment);

        abort_if($comment->ticket_id !== $ticket->id, 404);

        $comment->delete();

        return response()->json([
            'message' => 'Comment deleted'
        ]);
    }

    #[OA\Patch(
        path: '/api/tickets/{ticket}/internal-comments',
        summary: 'Create internal comment (Admin/Agent only)',
        tags: ['Comments'],
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
                required: ['comment'],
                properties: [
                    new OA\Property(
                        property: 'comment',
                        type: 'string',
                        example: 'Internal comment'
                    ),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: 'Comment created'),
            new OA\Response(response: 401, description: 'Unauthorized'),
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

        return response()->json(
            new CommentResource($comment),
            201
        );
    }
}
