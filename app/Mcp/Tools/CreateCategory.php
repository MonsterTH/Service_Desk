<?php

namespace App\Mcp\Tools;

use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Tool;
use App\Models\Category;
use App\Http\Resources\CategoryResource;

#[Description('This Tool Creates a Category (admin only).')]
class CreateCategory extends Tool
{
    public function handle(Request $request): Response
    {
        /** @var \App\Models\User $user */
        $user = $request->user();

        if (!$user) {
            return Response::error('Unauthorized.');
        }

        if (!$user->hasRole('admin')) {
            return Response::error('Only admins can create categories.');
        }

        $data = $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_active'   => 'required|boolean',
        ]);

        $category = Category::create([
            'name'        => $data['name'],
            'description' => $data['description'] ?? null,
            'is_active'   => $data['is_active'],
        ]);

        return Response::json([
            'success'  => true,
            'category' => new CategoryResource($category),
        ]);
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'name' => $schema->string()
                ->description('Name of the category (e.g. "Electronics")')
                ->required(),
            'description' => $schema->string()
                ->description('Short description of the category')
                ->nullable(),
            'is_active' => $schema->boolean()
                ->description('Whether the category is active')
                ->required(),
        ];
    }
}
