<?php

namespace App\Policies;

use App\Models\Milestone;
use App\Models\User;

class MilestonePolicy
{
    /**
     * Admins, or the client who owns the project, may approve a milestone,
     * request a revision, or comment on it.
     */
    public function respond(User $user, Milestone $milestone): bool
    {
        return $user->role === 'admin'
            || (int) $milestone->project?->client_id === (int) $user->id;
    }
}