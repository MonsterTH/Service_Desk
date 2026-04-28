<?php

namespace App\Mcp\Servers;

use Laravel\Mcp\Server;
use Laravel\Mcp\Server\Attributes\Instructions;
use Laravel\Mcp\Server\Attributes\Name;
use Laravel\Mcp\Server\Attributes\Version;
use App\Mcp\Tools\CreateCategory;
use App\Mcp\Tools\DeleteCategory;
use App\Mcp\Tools\UpdateCategory;
use App\Mcp\Resources\GetAllCategories;
use App\Mcp\Resources\GetCategory;

#[Name('CategoriesServer')]
#[Version('0.0.1')]
#[Instructions(
        'This server allows managing support categories.
        Use CreateCategory to create categories.
        Use DeleteCategory to soft delete (only when explicitly requested) a category.
        Always confirm before deleting a category.
        Use UpdateCategory to update a category.
    ')]
class CategoriesServer extends Server
{
    protected array $tools = [
        CreateCategory::class,
        DeleteCategory::class,
        UpdateCategory::class,
    ];

    protected array $resources = [
        GetAllCategories::class,
        GetCategory::class
    ];

    protected array $prompts = [
        //
    ];
}
