<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use OpenApi\Attributes as OA;
class CategoryController extends Controller
{
    #[OA\Get(
        path: '/api/categories',
        summary: 'List all categories',
        tags: ['Categories'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'List of categories',
                content: new OA\JsonContent(type: 'array', items: new OA\Items(
                    properties: [
                        new OA\Property(property: 'id', type: 'integer'),
                        new OA\Property(property: 'name', type: 'string'),
                        new OA\Property(property: 'description', type: 'string'),
                        new OA\Property(property: 'is_active', type: 'boolean'),
                    ]
                ))
            )
        ]
    )]
    public function index()
    {
        return response()->json(Category::all());
    }

    #[OA\Post(
        path: '/api/categories',
        summary: 'Create a new category',
        tags: ['Categories'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['name', 'description', 'is_active'],
                properties: [
                    new OA\Property(property: 'name', type: 'string', example: 'My category'),
                    new OA\Property(property: 'description', type: 'string', example: 'Category description'),
                    new OA\Property(property: 'is_active', type: 'boolean', example: true),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: 'Category created'),
            new OA\Response(response: 422, description: 'Validation error'),
        ]
    )]

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $category = Category::create([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'is_active' => $request->boolean('is_active', true),
        ]);

        return response()->json($category, 201);
    }

    #[OA\Get(
        path: '/api/categories/{id}',
        summary: 'Get a category by ID',
        tags: ['Categories'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(response: 200, description: 'Category found'),
            new OA\Response(response: 404, description: 'Category not found'),
        ]
    )]
    public function show(Category $category)
    {
        return response()->json($category);
    }

    #[OA\Put(
        path: '/api/categories/{id}',
        summary: 'Update a category',
        tags: ['Categories'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))
        ],
        requestBody: new OA\RequestBody(
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'name', type: 'string'),
                    new OA\Property(property: 'description', type: 'string'),
                    new OA\Property(property: 'is_active', type: 'boolean'),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Category updated'),
            new OA\Response(response: 404, description: 'Category not found'),
        ]
    )]
    public function update(Request $request, int $id)
    {
        $category = Category::findOrFail($id);

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $category->update($validated);

        return response()->json($category);
    }

    #[OA\Delete(
        path: '/api/categories/{id}',
        summary: 'Delete a category',
        tags: ['Categories'],
        parameters: [
            new OA\Parameter(
                name: 'id',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer')
            )
        ],
        responses: [
            new OA\Response(response: 200, description: 'Category deleted'),
            new OA\Response(response: 404, description: 'Category not found'),
        ]
    )]
    public function destroy(int $id)
    {
        $category = Category::findOrFail($id);

        $category->delete();

        return response()->json(['message' => 'Category deleted']);
    }
}
