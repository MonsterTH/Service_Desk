<?php

namespace App\Mcp\Tools;

use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Tool;
use App\Models\Category;
use App\Http\Resources\CategoryResource;

#[Description('Update a category (admin only).')]
class UpdateCategory extends Tool
{
    public function handle(Request $request): Response
    {
        /** @var \App\Models\User $user */
        $user = $request->user();

        if (!$user) {
            return Response::error('Unauthorized.');
        }

        if (!$user->hasRole('admin')) {
            return Response::error('Only admins can update categories.');
        }

        $data = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name'        => 'sometimes|string|max:255',
            'description' => 'sometimes|nullable|string',
            'is_active'   => 'sometimes|boolean',
        ]);

        $category = Category::findOrFail($data['category_id']);

        $category->update([
            'name'        => $data['name'] ?? $category->name,
            'description' => array_key_exists('description', $data)
                ? $data['description']
                : $category->description,
            'is_active'   => $data['is_active'] ?? $category->is_active,
        ]);

        return Response::json([
            'success'  => true,
            'category' => new CategoryResource($category->fresh()),
        ]);
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'category_id' => $schema->integer()
                ->description('ID of the category to update')
                ->required(),
            'name' => $schema->string()
                ->description('New category name')
                ->nullable(),
            'description' => $schema->string()
                ->description('New description')
                ->nullable(),
            'is_active' => $schema->boolean()
                ->description('Active state')
                ->nullable(),
        ];
    }
}
