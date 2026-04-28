<?php

namespace App\Mcp\Resources;

use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Resource;
use App\Models\Category;
use App\Http\Resources\CategoryResource;

#[Description('Get a single category by ID.')]
class GetCategory extends Resource
{
    public function handle(Request $request): Response
    {
        $user = $request->user();

        if (!$user) {
            return Response::error('Unauthorized.');
        }

        $data = $request->validate([
            'category_id' => 'required|exists:categories,id',
        ]);

        $category = Category::findOrFail($data['category_id']);

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
