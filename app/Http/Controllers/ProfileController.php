<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;
use App\Models\Report;
use App\Services\ActivityLoggerService;

class ProfileController extends Controller
{
    protected $logger;

    public function __construct(ActivityLoggerService $logger)
    {
        $this->logger = $logger;
    }

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
            'joined_at' => $user->created_at->translatedFormat('d F Y'),
            'is_verified' => $user->hasVerifiedEmail(),
            'is_active_reporter' => $reportCounts->total > 3,
        ];

        return Inertia::render('Profile/Edit', [
            'mustVerifyEmail' => $user instanceof MustVerifyEmail,
            'status' => session('status'),
            'stats' => $stats,
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();
        $user->fill($request->validated());

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        $this->logger->log('Update Profil', 'Pengguna memperbarui data profil');

        return Redirect::route('profile.edit');
    }

    /**
     * Update the user's avatar/profile photo.
     */
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

        $this->logger->log('Update Avatar', 'Pengguna mengganti foto profil');

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

        $this->logger->log('Hapus Akun', 'Pengguna menghapus akun');

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
