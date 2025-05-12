<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determine the current asset version.
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        $user = $request->user();
    
        return array_merge(parent::share($request), [
            'auth' => [
                'user' => $user ? [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'roles' => $user->getRoleNames(),
                    'permissions' => $user->getPermissionNames(),
                    'no_hp' => $user->no_hp,
                    'email_verified_at' => $user->email_verified_at,
                    'no_hp_verified_at' => $user->no_hp_verified_at,
                    'avatar_url' => $user && $user->avatar
                        ? asset('storage/' . $user->avatar)
                        : asset('/Default-Profile.png'),
                ] : null,
    
                'isAdmin' => $user?->hasAnyRole(['admin', 'verifier']),
    
                'needsEmailVerification' => $user?->email && !$user?->hasVerifiedEmail(),
                'needsPhoneVerification' => $user?->no_hp && !$user?->hasVerifiedPhone(),

               
            ],
        ]);
    }
    
}
