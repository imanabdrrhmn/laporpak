<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

class EnsureContactIsVerified
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        if (!$user) {
            return redirect()->route('login');
        }

        $emailVerified = $user->email ? $user->hasVerifiedEmail() : false;
        $phoneVerified = $user->no_hp ? $user->hasVerifiedPhone() : false;

        if (!$emailVerified && !$phoneVerified) {
            if ($user->email && !Route::is('verification.notice')) {
                return redirect()->route('verification.notice');
            }

            if ($user->no_hp && !Route::is('verification.phone.notice')) {
                return redirect()->route('verification.phone.notice');
            }
        }

        return $next($request);
    }
}
