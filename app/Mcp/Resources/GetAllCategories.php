<?php

namespace App\Mcp\Resources;

use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Resource;
use App\Models\Category;
use App\Http\Resources\CategoryResource;

#[Description('List all categories.')]
class GetAllCategories extends Resource
{
    public function handle(Request $request): Response
    {
        $user = $request->user();

        if (!$user) {
            return Response::error('Unauthorized.');
        }

        $data = $request->validate([
            'page'         => 'sometimes|integer|min:1',
            'ItemsPerPage' => 'sometimes|integer|min:1|max:100',
        ]);

        $paginator = Category::paginate(
            $data['ItemsPerPage'] ?? 5,
            ['*'],
            'page',
            $data['page'] ?? 1
        );

        return Response::json([
            'data' => CategoryResource::collection($paginator->items()),
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'last_page'    => $paginator->lastPage(),
                'total'        => $paginator->total(),
            ],
        ]);
    }

    public function schema(\Illuminate\Contracts\JsonSchema\JsonSchema $schema): array
    {
        return [
            'page'         => $schema->integer()->nullable(),
            'ItemsPerPage' => $schema->integer()->nullable(),
        ];
    }
}
