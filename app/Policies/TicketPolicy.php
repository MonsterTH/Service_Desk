<?php

namespace App\Policies;

use App\Models\Ticket;
use App\Models\User;

class TicketPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    private function isAssignedToUser(User $user, Ticket $ticket): bool
    {
        return $ticket->assigned_to === $user->id;
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
        if ($ticket->trashed()) {
            return $user->hasRole('admin');
        }

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
        if ($ticket->isFinalState()) {
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

    public function assign(User $user, Ticket $ticket, ?User $targetUser = null): bool
    {
        if ($ticket->isFinalState()) {
            return false;
        }

        if ($user->hasRole('agent')) {
            // Agent só pode atribuir a si mesmo
            return $targetUser && $targetUser->id === $user->id;
        }

        if ($user->hasRole('admin')) {
            // Admin só pode atribuir a agents ou a si mesmo
            return $targetUser && (
                $targetUser->hasRole('agent') || $targetUser->id === $user->id
            );
        }

        return false;
    }

    public function updateStatus(User $user, Ticket $ticket): bool
    {
        if ($ticket->isFinalState()) {
            return false;
        }

        return $user->hasRole('admin')
            || ($user->hasRole('agent') && $this->isAssignedToUser($user, $ticket));
    }

    public function updatePriority(User $user, Ticket $ticket): bool
    {
        if ($ticket->isFinalState()) {
            return false;
        }

        return $user->hasRole('admin')
            || ($user->hasRole('agent') && $this->isAssignedToUser($user, $ticket));
    }

    public function internal_comments(User $user, Ticket $ticket): bool
    {
        if ($ticket->isFinalState()) {
            return false;
        }

        if (! $user->hasAnyRole(['admin', 'agent'])) {
            return false;
        }

        if ($user->hasRole('agent')) {
            return $ticket->assigned_to === $user->id;
        }

        return true;
    }
}
