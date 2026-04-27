<?php

namespace App\Mcp\Resources;

use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Resource;
use App\Models\Category;
use App\Http\Resources\CategoryResource;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

#[Description('Get a single category by ID.')]
class GetCategory extends Resource
{
    use AuthorizesRequests;

    public function handle(Request $request): Response
    {
        $data = $request->validate([
            'category_id' => 'required|exists:categories,id',
        ]);

        $category = Category::findOrFail($data['category_id']);

        $this->authorize('view', $category);

        return Response::json([
            'data' => new CategoryResource($category),
        ]);
    }

    public function schema(\Illuminate\Contracts\JsonSchema\JsonSchema $schema): array
    {
        return [
            'category_id' => $schema->integer()
                ->description('ID of the category to retrieve')
                ->required(),
        ];
    }
}
