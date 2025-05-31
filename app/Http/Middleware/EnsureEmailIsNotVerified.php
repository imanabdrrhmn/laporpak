<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureEmailIsNotVerified
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        if (!$user) {
            return redirect()->route('login');
        }

        if (empty($user->email)) {
            return redirect()->route('dashboard'); 
        }

        if ($user->hasVerifiedEmail()) {
            return redirect()->route('dashboard');
        }

        return $next($request);
    }
}
