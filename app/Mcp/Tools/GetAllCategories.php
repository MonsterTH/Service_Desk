<?php

namespace App\Mcp\Tools;

use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Tool;
use App\Models\Category;
use App\Http\Resources\CategoryResource;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

#[Description('List all categories.')]
class GetAllCategories extends Tool
{
    use AuthorizesRequests;

    public function handle(Request $request): Response
    {
        $this->authorize('viewAny', Category::class);

        $data = $request->validate([
            'page' => 'sometimes|integer|min:1',
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
                'last_page' => $paginator->lastPage(),
                'total' => $paginator->total(),
            ],
        ]);
    }

    public function schema(\Illuminate\Contracts\JsonSchema\JsonSchema $schema): array
    {
        return [
            'page' => $schema->integer()->nullable(),
            'ItemsPerPage' => $schema->integer()->nullable(),
        ];
    }
}
