<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsurePhoneNumberIsNotVerified
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        if (!$user || ($user->no_hp && $user->hasVerifiedPhone())) {
            return redirect()->route('dashboard');
        }
        return $next($request);
    }
}
