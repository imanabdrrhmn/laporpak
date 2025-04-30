<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Feedback;

class FeedbackPolicy
{
    public function update(User $user, Feedback $feedback): bool
    {
        return $user->id === $feedback->user_id || $user->hasRole('admin') ;
    }

    public function delete(User $user, Feedback $feedback): bool
    {
        return $user->hasRole('admin') || $user->id === $feedback->user_id;
    }

    public function create(User $user): bool
    {
        return !$user->feedback()->exists();
    }
}
