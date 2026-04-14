<?php

namespace App\Policies;

use App\Models\Ticket;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class TicketPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    private function isAssignedToUser(User $user, Ticket $ticket): bool
    {
        return $ticket->assigned_to === $user->id;
    }

    private function isFinalState(Ticket $ticket): bool
    {
        return in_array($ticket->status, ['resolved', 'closed']);
    }

    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Ticket $ticket): bool
    {
        return $user->hasRole('admin')
            || ($user->hasRole('agent') && $ticket->assigned_to === $user->id)
            || $ticket->created_by === $user->id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Ticket $ticket): bool
    {
        if ($this->isFinalState($ticket)) {
            return false;
        }

        return $user->hasRole('admin')
            || ($user->hasRole('agent') && $this->isAssignedToUser($user, $ticket))
            || ($user->hasRole('employee') && $ticket->created_by === $user->id);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Ticket $ticket): bool
    {
        return $user->hasRole('admin');
    }

    public function assign(User $user, Ticket $ticket): bool
    {
        if ($this->isFinalState($ticket)) {
            return false;
        }

        return $user->hasRole('admin') || $user->hasRole('agent');
    }

    public function updateStatus(User $user, Ticket $ticket): bool
    {
        if ($this->isFinalState($ticket)) {
            return false;
        }

        return $user->hasRole('admin')
            || ($user->hasRole('agent') && $this->isAssignedToUser($user, $ticket));
    }

    public function updatePriority(User $user, Ticket $ticket): bool
    {
        if ($this->isFinalState($ticket)) {
            return false;
        }

        return $user->hasRole('admin')
            || ($user->hasRole('agent') && $this->isAssignedToUser($user, $ticket));
    }
}
