<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsurePhoneNumberIsNotVerified
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        if (!$user) {
            return redirect()->route('login');
        }

        if (empty($user->no_hp)) {
            return redirect()->route('dashboard');
        }

        if ($user->hasVerifiedPhone()) {
            return redirect()->route('dashboard');
        }

        return $next($request);
    }
}
