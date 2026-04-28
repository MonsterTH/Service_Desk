<?php

namespace App\Mcp\Tools;

use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Tool;
use Laravel\Mcp\Server\Tools\Annotations\IsDestructive;
use App\Models\Category;

#[Description('Delete a category (admin only).')]
#[IsDestructive]
class DeleteCategory extends Tool
{
    public function handle(Request $request): Response
    {
        /** @var \App\Models\User $user */
        $user = $request->user();

        if (!$user) {
            return Response::error('Unauthorized.');
        }

        if (!$user->hasRole('admin')) {
            return Response::error('Only admins can delete categories.');
        }

        $data = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'confirm'     => 'required|boolean',
        ]);

        if (!$data['confirm']) {
            return Response::error('You must confirm before deleting this category.');
        }

        $category = Category::findOrFail($data['category_id']);

        if ($category->tickets()->exists()) {
            return Response::error('Cannot delete a category that has tickets associated.');
        }

        if ($category->is_active) {
            return Response::error('Cannot delete an active category. Deactivate it first.');
        }

        $category->delete();

        return Response::json([
            'message' => 'Category deleted successfully.',
        ]);
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'category_id' => $schema->integer()
                ->description('ID of the category to delete')
                ->required(),
            'confirm' => $schema->boolean()
                ->description('Must be TRUE only if the user explicitly confirmed deletion (never guess)')
                ->required(),
        ];
    }
}
