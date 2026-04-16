<?php

namespace App\OpenApi;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'Ticket',
    type: 'object',
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 1),
        new OA\Property(property: 'title', type: 'string', example: 'My ticket'),
        new OA\Property(property: 'description', type: 'string', nullable: true, example: 'Ticket description'),
        new OA\Property(property: 'status', type: 'string', enum: ['open', 'in_progress', 'resolved', 'closed'], example: 'open'),
        new OA\Property(property: 'priority', type: 'string', enum: ['low', 'medium', 'high', 'urgent'], example: 'low'),
        new OA\Property(property: 'category_id', type: 'integer', nullable: true, example: 1),
        new OA\Property(property: 'assigned_to', type: 'integer', nullable: true, example: 2),
        new OA\Property(property: 'created_by', type: 'integer', example: 1),
        new OA\Property(
            property: 'category',
            type: 'object',
            nullable: true,
            properties: [
                new OA\Property(property: 'id', type: 'integer', example: 1),
                new OA\Property(property: 'name', type: 'string', example: 'Hardware'),
            ]
        ),
        new OA\Property(
            property: 'creator',
            type: 'object',
            nullable: true,
            properties: [
                new OA\Property(property: 'id', type: 'integer', example: 1),
                new OA\Property(property: 'name', type: 'string', example: 'John Doe'),
            ]
        ),
        new OA\Property(
            property: 'assignee',
            type: 'object',
            nullable: true,
            properties: [
                new OA\Property(property: 'id', type: 'integer', example: 2),
                new OA\Property(property: 'name', type: 'string', example: 'Support Agent'),
            ]
        ),
    ]
)]
class TicketSchema {}
