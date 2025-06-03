<?php

namespace App\Http\Controllers\Auth;


use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
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
            return redirect()->route('dashboard.admin.page');
        } else {
            return redirect(url()->previous());
        }

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

            $avatarUrl = $googleUser->getAvatar();
            $newAvatarPath = null;

            try {
                $avatarContents = Http::get($avatarUrl)->body();
                $newAvatarPath = 'avatars/' . Str::uuid() . '.jpg';
                Storage::disk('public')->put($newAvatarPath, $avatarContents);
            } catch (\Exception $e) {
                \Log::warning('Gagal download avatar dari Google: ' . $e->getMessage());
                $newAvatarPath = null;
            }

            $user = User::where('email', $googleUser->getEmail())->first();

            if ($user) {
                $updated = false;

                if (!$user->google_id) {
                    $user->google_id = $googleUser->getId();
                    $updated = true;
                }

                if ($newAvatarPath && $newAvatarPath !== $user->avatar) {
                    if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                        Storage::disk('public')->delete($user->avatar);
                    }

                    $user->avatar = $newAvatarPath;
                    $updated = true;
                }

                if ($updated) {
                    $user->save();
                }
            } else {
                $user = User::create([
                    'email' => $googleUser->getEmail(),
                    'name' => $googleUser->getName(),
                    'google_id' => $googleUser->getId(),
                    'avatar' => $newAvatarPath,
                    'password' => bcrypt(Str::random(12)),
                ]);
            }

            if ($user->roles->isEmpty()) {
                $user->assignRole('user');
            }

            Auth::login($user);
            session()->regenerate();

            return redirect()->to(session()->pull('url.intended', '/'));
        } catch (\Exception $e) {
            \Log::error('Error saat login Google: ' . $e->getMessage());
            return redirect('/login')->with('error', 'Login dengan Google gagal.');
        }
    }


}
