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
            || $user->hasRole('agent')
            || $ticket->created_by === $user->id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasRole('employee')
            || $user->hasRole('admin');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Ticket $ticket): bool
    {
        return $user->hasRole('admin')
            || $user->hasRole('agent')
            || $ticket->created_by === $user->id;
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
        return $user->hasAnyRole(['agent', 'admin']);
    }

    public function updateStatus(User $user, Ticket $ticket): bool
    {
        return $user->hasAnyRole(['agent', 'admin']);
    }
}
