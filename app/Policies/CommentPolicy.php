<?php

namespace App\Policies;

use App\Models\Comment;
use App\Models\User;
use App\Models\Ticket;
use Illuminate\Auth\Access\Response;

class CommentPolicy
{
    public function viewAny(User $user, Ticket $ticket): bool
    {
        if ($user->hasRole('admin')) {
            return true;
        }

        if ($user->hasRole('agent')) {
            return $ticket->assigned_to === $user->id;
        }

        if ($user->hasRole('employee')) {
            return $ticket->created_by === $user->id;
        }

        return false;
    }

    public function view(User $user, Comment $comment): bool
    {
        if ($comment->trashed()) {
            return $user->hasRole('admin');
        }

        if ($user->hasRole('admin')) {
            return true;
        }

        if ($comment->user_id === $user->id) {
            return true;
        }

        if ($user->hasRole('agent')) {
            return $comment->ticket->assigned_to === $user->id;
        }

        return false;
    }

    public function create(User $user, Ticket $ticket): bool
    {
        if ($ticket->isFinalState()) {
            return false;
        }

        if ($user->hasRole('admin')) {
            return true;
        }

        if ($user->hasRole('employee')) {
            return $ticket->created_by === $user->id;
        }

        if ($user->hasRole('agent')) {
            return $ticket->assigned_to === $user->id;
        }

        return false;
    }

    public function update(User $user, Comment $comment): bool
    {
        $ticket = $comment->ticket;

        if ($ticket->isFinalState()) {
            return false;
        }

        return $comment->user_id === $user->id;
    }

    public function delete(User $user, Comment $comment): bool
    {
        return $user->hasRole('admin')
            || $comment->user_id === $user->id;
    }
}
