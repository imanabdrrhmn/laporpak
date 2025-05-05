<?php

namespace App\Providers;

use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;
use Inertia\Inertia;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void    
    {
        Vite::prefetch(concurrency: 3);
        Inertia::share([
            'flash' => function () {
                return [
                    'success' => session('success'),
                    'error' => session('error'),
                ];
            },
            'auth' => function () {
                $user = auth()->user();
        
                return $user ? [
                    'user' => $user,
                    'needsEmailVerification' => $user->email && !$user->hasVerifiedEmail(),
                    'needsPhoneVerification' => $user->no_hp && !$user->hasVerifiedPhone(),
                ] : null;
            },
            'contact.verified' => function () {
                if (!auth()->check()) return false;
        
                $user = auth()->user();
                return (
                    ($user->email && $user->hasVerifiedEmail()) ||
                    ($user->no_hp && $user->hasVerifiedPhone())
                );
            },
        ]);        
    }
}
