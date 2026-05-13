<?php

namespace App\Mcp\Tools;

use App\Models\Category;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Tool;
use Laravel\Mcp\Server\Attributes\Description;

#[Description('Activate or deactivate a category')]
class ToggleCategory extends Tool
{
    public function handle(Request $request): Response
    {
        /** @var \App\Models\User $user */
        $user = $request->user();

        $category = Category::find($request->get('category_id'));

        if (! $category) {
            return Response::error('Category not found');
        }

        if (! $user->can('toggle', $category)) {
            return Response::error('Unauthorized');
        }

        if (
            $category->is_active &&
            $category->tickets()->exists()
        ) {

            return Response::error('Cannot deactivate a category that has tickets associated.');
        }

        $category->update([
            'is_active' => ! $category->is_active,
        ]);

        return Response::json([
            'category_id' => $category->id,
            'is_active' => $category->is_active,
            'message' => $category->is_active
                ? 'Category activated'
                : 'Category deactivated',
        ]);
    }

    public function schema(
        \Illuminate\Contracts\JsonSchema\JsonSchema $schema
    ): array {
        return [
            'category_id' => $schema->integer(),
        ];
    }
}
