<?php

namespace App\Mcp\Tools;

use App\Models\Category;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Tool;
use Laravel\Mcp\Server\Attributes\Description;

#[Description('Get ticket statistics for a category')]
class GetCategoryStats extends Tool
{
    public function handle(Request $request): Response
    {
        /** @var \App\Models\User $user */
        $user = $request->user();

        $category = Category::find($request->get('category_id'));

        if (! $category) {
            return Response::error('Category not found');
        }

        // autorização igual ao controller
        if (! $user->can('stats', $category)) {
            return Response::error('Unauthorized');
        }

        return Response::json([
            'category_id' => $category->id,
            'stats' => [
                'total' => $category->tickets()->count(),
                'open' => $category->tickets()->where('status', 'open')->count(),
                'in_progress' => $category->tickets()->where('status', 'in_progress')->count(),
                'resolved' => $category->tickets()->where('status', 'resolved')->count(),
                'closed' => $category->tickets()->where('status', 'closed')->count(),
            ],
        ]);
    }

    public function schema(\Illuminate\Contracts\JsonSchema\JsonSchema $schema): array
    {
        return [
            'category_id' => $schema->integer(),
        ];
    }
}
