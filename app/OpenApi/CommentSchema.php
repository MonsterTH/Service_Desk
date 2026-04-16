<?php

namespace App\OpenApi;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'Comment',
    type: 'object',
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 1),
        new OA\Property(property: 'ticket_id', type: 'integer', example: 10),
        new OA\Property(property: 'user_id', type: 'integer', example: 5),
        new OA\Property(property: 'comment', type: 'string', example: 'This is a comment'),
        new OA\Property(property: 'is_internal', type: 'boolean', example: false),
        new OA\Property(
            property: 'ticket',
            type: 'object',
            properties: [
                new OA\Property(property: 'id', type: 'integer', example: 2),
                new OA\Property(property: 'title', type: 'string', example: 'My ticket'),
            ]
        ),
        new OA\Property(
            property: 'user',
            type: 'object',
            properties: [
                new OA\Property(property: 'id', type: 'integer', example: 2),
                new OA\Property(property: 'name', type: 'string', example: 'Support Agent'),
            ]
        ),
    ]
)]
class CommentSchema {}
