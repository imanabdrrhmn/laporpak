<?php

namespace App\Policies;

use App\Models\User;
use App\Models\TopUp;

class TopUpPolicy
{
    public function viewAny(User $user)
    {
        return $user->can('view_topup');
    }

    public function verify(User $user, TopUp $topUp)
    {
        return $user->can('verify_topup');
    }

    public function reject(User $user, TopUp $topUp)
    {
        return $user->can('verify_topup');
    }
}
