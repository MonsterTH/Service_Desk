<?php

namespace App\Mcp\Tools;

use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Tool;
use App\Models\Ticket;
use App\Http\Resources\TicketResource;

#[Description('Create a ticket for the Service Desk Server.')]
class CreateTicket extends Tool
{
    /**
     * Handle the tool request.
     */
    public function handle(Request $request): Response
    {
        $data = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'category_id' => ['required','exists:categories,id'],
        ]);

        $ticket = Ticket::create([
            'title'       => $data['title'],
            'description' => $data['description'] ?? null,
            'status'      => 'open',
            'priority'    => 'low',
            'category_id' => $data['category_id'],
            'created_by'  => $request->user()->id,
        ]);

        return Response::json([
            'success' => true,
            'ticket' => [
                'id'          => $ticket->id,
                'title'       => $ticket->title,
                'description' => $ticket->description,
                'status'      => $ticket->status,
                'priority'    => $ticket->priority,
                'category_id' => $ticket->category_id,
                'assigned_to' => $ticket->assigned_to,
                'created_by'  => $ticket->created_by,
                'created_at'  => $ticket->created_at,
                'updated_at'  => $ticket->updated_at,
            ],
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
            'title' => $schema->string()
                ->description('Short summary of the issue (e.g., "Email not working")')
                ->required(),

            'description' => $schema->string()
                ->description('Detailed explanation of the problem')
                ->nullable(),

            'category_id' => $schema->integer()
                ->description('Category ID (must exist in categories table)')
                ->required(),
        ];
    }

    public function outputSchema(JsonSchema $schema): array
    {
        return [];
    }
}
