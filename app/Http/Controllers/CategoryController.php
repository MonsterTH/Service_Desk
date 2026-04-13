<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;

class CategoryController extends Controller
{
    /**
     * GET /categories
     */
    public function index()
    {
        return Category::all();
    }

    /**
     * POST /categories
     */
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
            'is_active' => $validated['is_active'] ?? true,
        ]);

        return response()->json($category, 201);
    }

    /**
     * GET /categories/{id}
     */
    public function show(string $id)
    {
        $category = Category::findOrFail($id);

        return response()->json($category);
    }

    /**
     * PUT /categories/{id}
     */
    public function update(Request $request, string $id)
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

    /**
     * DELETE /categories/{id}
     */
    public function destroy(string $id)
    {
        $category = Category::findOrFail($id);

        $category->delete();

        return response()->json(['message' => 'Category deleted']);
    }
}
