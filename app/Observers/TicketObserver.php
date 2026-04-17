<?php

namespace App\Observers;

use App\Models\Ticket;
use App\Models\TicketLog;
use Illuminate\Support\Facades\Auth;
use App\Models\Category;
use Illuminate\Validation\ValidationException;

class TicketObserver
{
    public function created(Ticket $ticket): void
    {
        TicketLog::create([
            'ticket_id' => $ticket->id,
            'user_id'   => Auth::id(),
            'action'    => 'created',
            'changes'   => null,
        ]);
    }

    public function updated(Ticket $ticket): void
    {
        $dirty = $ticket->getDirty();
        $original = $ticket->getOriginal();

        $watchedFields = ['status', 'priority', 'assigned_to', 'title', 'description', 'category_id'];

        foreach ($watchedFields as $field) {
            if (array_key_exists($field, $dirty)) {
                TicketLog::create([
                    'ticket_id' => $ticket->id,
                    'user_id'   => Auth::id(),
                    'action'    => $field . '_changed',
                    'changes'   => [
                        'from' => $original[$field],
                        'to'   => $dirty[$field],
                    ],
                ]);
            }
        }
    }

    public function saving(Ticket $ticket): void
    {
        if ($ticket->category_id) {
            $category = Category::find($ticket->category_id);

            if (! $category || ! $category->is_active) {
                throw ValidationException::withMessages([
                    'category_id' => 'Only active categories can be assigned to tickets.',
                ]);
            }
        }
    }

    public function deleted(Ticket $ticket): void
    {
        TicketLog::create([
            'ticket_id' => $ticket->id,
            'user_id'   => Auth::id(),
            'action'    => 'deleted',
            'changes'   => null,
        ]);
    }
}
