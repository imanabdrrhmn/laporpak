<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Support\Facades\Storage;
use App\Models\Report;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): Response
    {
    $user = $request->user();
    $reportCounts = Report::selectRaw("
            COUNT(*) as total,
            SUM(CASE WHEN status = 'Published' THEN 1 ELSE 0 END) as selected,
            SUM(CASE WHEN status = 'Pending' THEN 1 ELSE 0 END) as in_process
        ")
        ->where('user_id', $user->id)
        ->first();

    $stats = [
        'reports' => $reportCounts->total ?? 0,
        'selected' => $reportCounts->selected ?? 0,
        'in_process' => $reportCounts->in_process ?? 0,
        // 'last_login' => optional($user->last_login_at)->diffForHumans() ?? 'Belum pernah login',
        'joined_at' => $user->created_at->translatedFormat('d F Y'),
        'is_verified' => $user->hasVerifiedEmail(),
        'is_active_reporter' => $reportCounts->total > 3,
    ];
        return Inertia::render('Profile/Edit', [
            'mustVerifyEmail' => $request->user() instanceof MustVerifyEmail,
            'status' => session('status'),
            'stats' => $stats,
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit');
    }


    public function updateAvatar(Request $request): RedirectResponse
    {
        $request->validate([
            'avatar' => ['required', 'image', 'max:2048'], 
        ]);

        $user = $request->user();

        if ($user->avatar) {
            Storage::disk('public')->delete($user->avatar);
        }

        $path = $request->file('avatar')->store('avatars', 'public');
        $user->avatar = $path;
        $user->save();

        return redirect()->route('profile.edit')->with('status', 'Avatar updated.');
    }


    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
