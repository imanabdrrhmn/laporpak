<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class AuthenticatedSessionController extends Controller
{
    
    /**
     * Handle login via email or no_hp.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => 'nullable|email',
            'no_hp' => 'nullable|string',
            'password' => 'required|string',
        ], [
            'email.email' => 'Format email tidak valid',
            'password.required' => 'Kata sandi wajib diisi',
        ]);
        
    
        $user = null;

        if ($request->filled('email')) {
            $user = User::where('email', $request->email)->first();
        } elseif ($request->filled('no_hp')) {
            $user = User::where('no_hp', $request->no_hp)->first();
        }

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'password' => ['Email, No HP, atau password salah.'],
            ]);
        }
        
        Auth::login($user);
        $request->session()->regenerate();

        if ($user->hasRole('admin') || $user->hasRole('verifier')) {
            return redirect()->route('dashboard');
        }

        return redirect(url()->previous());
    }

    /**
     * Logout the user.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }   

        public function redirectToGoogle()
    {
        session(['url.intended' => url()->previous()]); 
        return Socialite::driver('google')->redirect();
    }


    public function handleGoogleCallback()
{
    try {
        $googleUser = Socialite::driver('google')->user();
        
        $user = User::firstOrCreate([
            'email' => $googleUser->getEmail(),
        ], [
            'name' => $googleUser->getName(),
            'google_id' => $googleUser->getId(),
            'avatar' => $googleUser->getAvatar(),
            'password' => bcrypt(uniqid()) 
        ]);

        if (!$user->hasRole('user')) {
            $user->assignRole('user');
        }

        Auth::login($user);
        session()->regenerate();

        return redirect()->to(session()->pull('url.intended', '/'));

        } catch (\Exception $e) {
            return redirect('/login')->with('error', 'Login dengan Google gagal.');
        }
    }

}
