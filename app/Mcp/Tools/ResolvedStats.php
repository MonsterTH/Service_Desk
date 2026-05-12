<?php

namespace App\Mcp\Tools;

use App\Models\User;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Tool;

#[Description('Get resolved and closed tickets count by agents and admins')]
class ResolvedStats extends Tool
{
    /**
     * Handle the tool request.
     */
    public function handle(Request $request): Response
    {
        $stats = User::role(['admin', 'agent'])
            ->withCount([
                'assignedTickets as resolved_tickets_count' => function ($query) {
                    $query->whereIn('status', ['resolved', 'closed']);
                }
            ])
            ->get(['id', 'name'])
            ->map(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'resolved_tickets_count' => $user->resolved_tickets_count,
                ];
            });

        return Response::json($stats);
    }

    /**
     * Tool schema.
     */
    public function schema(JsonSchema $schema): array
    {
        return [];
    }
}
