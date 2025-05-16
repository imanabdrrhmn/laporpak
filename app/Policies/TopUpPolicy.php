<?php

// app/Policies/TopUpPolicy.php

namespace App\Policies;

use App\Models\User;
use App\Models\TopUp;

class TopUpPolicy
{
    public function viewAny(User $user)
    {
        return $user->role === 'admin' || 'verifier';
    }

    public function verify(User $user, TopUp $topUp)
    {
        return $user->role === 'admin' || 'verifier';
    }

    public function reject(User $user, TopUp $topUp)
    {
        return $user->role === 'admin' || 'verifier';
    }
}
