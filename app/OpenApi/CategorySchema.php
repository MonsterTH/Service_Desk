<?php

namespace App\OpenApi;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'Category',
    type: 'object',
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 1),
        new OA\Property(property: 'name', type: 'string', example: 'Electronics'),
        new OA\Property(property: 'description', type: 'string', example: 'All electronic items'),
        new OA\Property(property: 'is_active', type: 'boolean', example: true),
    ]
)]
class CategorySchema {}
