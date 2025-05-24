<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureEmailIsNotVerified
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        if (!$user || $user->hasVerifiedEmail()) {
            return redirect()->route('dashboard');
        }

        return $next($request);
    }
}
