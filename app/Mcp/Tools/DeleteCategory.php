<?php

namespace App\Mcp\Tools;

use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Tool;
use Laravel\Mcp\Server\Tools\Annotations\IsDestructive;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Models\Category;

#[Description('This Tool Deletes an Category (admin only).')]
#[IsDestructive]
class DeleteCategory extends Tool
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
            'category_id' => 'required|exists:categories,id',
            'confirm'   => 'required|boolean',
        ]);

        if (! $data['confirm']) {
            return Response::error('You must confirm before deleting this category.');
        }

        $category = Category::findOrFail($data['category_id']);

        $this->authorize('delete', $category);

        $category->delete();

        return Response::json([
            'message' => 'Category deleted successfully'
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
            'category_id' => $schema->integer()
                ->description('The ID of the category to delete')
                ->required(),

            'confirm' => $schema->boolean()
                ->description('Must be TRUE only if the user explicitly confirmed deletion (never guess)')
                ->required(),
        ];
    }
}
