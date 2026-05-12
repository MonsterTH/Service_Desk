<?php

namespace App\Jobs;

use App\Models\Ticket;
use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class EscalateUnassignedTickets
{
    use Dispatchable;

    public function handle(): void
    {
        $escalationMap = [
            'low'    => 'medium',
            'medium' => 'high',
            'high'   => 'urgent',
            'urgent' => 'urgent',
        ];

        Ticket::whereNull('assigned_to')
            ->whereNotIn('status', ['resolved', 'closed'])
            ->get()
            ->each(function ($ticket) use ($escalationMap) {
                $newPriority = $escalationMap[$ticket->priority] ?? $ticket->priority;

                if ($newPriority !== $ticket->priority) {
                    $ticket->update(['priority' => $newPriority]);
                }
            });
    }
}
