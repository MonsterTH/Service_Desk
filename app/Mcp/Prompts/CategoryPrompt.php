<?php

namespace App\Mcp\Prompts;

use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Attributes\Name;
use Laravel\Mcp\Server\Prompt;
use Laravel\Mcp\Server\Prompts\Argument;

#[Name('categories-assistant')]
#[Description('Service Desk assistant focused on category management.')]
class CategoryPrompt extends Prompt
{
    public function arguments(): array
    {
        return [
            new Argument(
                name: 'role',
                description: 'The role of the user (admin, agent, employee)',
                required: true,
            ),
            new Argument(
                name: 'context',
                description: 'What the user wants to do with categories',
                required: false,
            ),
        ];
    }

    public function handle(Request $request): array
    {
        $role    = $request->get('role', 'employee');
        $context = $request->get('context', '');

        $rules = match ($role) {
            'admin' => "
                - View all categories (active and inactive)
                - Create new categories
                - Update existing categories (name, description, is_active)
                - Delete categories (only if inactive and has no tickets associated)
                - Always confirm before deleting
            ",
            'agent' => "
                - View all categories
                - Cannot create, update or delete categories
            ",
            default => "
                - View all categories
                - Cannot create, update or delete categories
            ",
        };

        $systemMessage = "
            You are a Service Desk assistant specialized in category management.
            The current user role is: {$role}

            Permissions:
            {$rules}

            Available tools:
            - GetAllCategories: List all categories (page, ItemsPerPage)
            - GetCategory: Get a specific category by category_id
            - CreateCategory: Create a category (admin only, requires name and is_active)
            - UpdateCategory: Update a category (admin only, requires category_id; optional: name, description, is_active)
            - DeleteCategory: Delete a category (admin only, requires category_id and confirm=true)

            Important rules:
            - NEVER set confirm=true unless the user explicitly said yes
            - A category can only be deleted if it is inactive (is_active=false) and has no tickets
            - Always use GetAllCategories first if you don't know the category_id
            - Only admins can create, update or delete categories
            - Agents and employees can only view categories
        ";

        $userMessage = $context
            ? "I need help with categories: {$context}"
            : "I'm ready to help you manage your categories. What would you like to do?";

        return [
            Response::text($systemMessage)->asAssistant(),
            Response::text($userMessage),
        ];
    }
}
