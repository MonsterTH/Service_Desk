<?php

namespace App\Mcp\Tools;

use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Tool;
use App\Models\Category;
use App\Http\Resources\CategoryResource;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

#[Description('This Tool Creates an Category (admin only).')]
class CreateCategory extends Tool
{
    use AuthorizesRequests;
    /**
     * Handle the tool request.
     */
    public function handle(Request $request): Response
    {
        $user = $request->user();

        if (! $user) {
            return Response::error('Unauthorized');
        }

        $data = $request->validate([
            'name'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'required|boolean',
        ]);

        $this->authorize('create', Category::class);

        $category = Category::create([
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'is_active' => $data['is_active']
        ]);

        return Response::json([
            'success' => true,
            'category' => new CategoryResource($category)
        ]);
    }

    /**
     * Get the tool's input schema.
     *
     * @return array<string, JsonSchema>
     */
    public function schema(JsonSchema $schema): array
    {
        return [
            'name' => $schema->string()
                ->description('Name of a category (Example: Electronics)')
                ->required(),

            'description' => $schema->string()
                ->description('Small description of a category')
                ->nullable(),

            'is_active' => $schema->boolean()
                ->description('Defines if the category is active')
                ->required(),
        ];
    }
}
