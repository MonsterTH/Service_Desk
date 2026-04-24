<?php

namespace App\Mcp\Tools;

use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Tool;
use App\Models\Ticket;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

#[Description('Delete a ticket from the Service Desk.')]
class DeleteTicket extends Tool
{
    use AuthorizesRequests;
    /**
     * Handle the tool request.
     */
    public function handle(Request $request): Response
    {
        $user = $request->user();

        if (! $user) {
            return Response::error('Unauthorized');
        }

        $data = $request->validate([
            'ticket_id' => 'required|exists:tickets,id',
        ]);

        $ticket = Ticket::findOrFail($data['ticket_id']);

        $this->authorize('delete', $ticket);

        $ticket->delete();

        return Response::json([
            'message' => 'Ticket deleted successfully'
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
            'ticket_id' => $schema->integer()
                ->description('The ID of the ticket to delete')
                ->required(),
        ];
    }
}
