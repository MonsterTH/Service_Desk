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
#[Version('1.0.0')]
#[Instructions(
        'This server allows managing support categories.
        Use CreateTicket to create categories.
        Use DeleteTicket to soft delete (only when explicitly requested) a category.
        Always confirm before deleting a category.
        Use UpdateTicket to update a category.
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
