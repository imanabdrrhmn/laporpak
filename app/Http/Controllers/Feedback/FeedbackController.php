<?php

namespace App\Http\Controllers\Feedback;

use App\Models\Feedback;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class FeedbackController extends Controller
{
    use AuthorizesRequests;

    public function index(Request $request): Response
    {
        $user = $request->user();
        $contactVerified = $user && ($user->email_verified_at || $user->no_hp_verified_at);
        $feedbacks = Feedback::with('user')->latest()->get();
        $canGiveFeedback = $user ? !Feedback::where('user_id', $user->id)->exists() : false;

        return Inertia::render('Feedback/FeedbackPage', [
            'feedbacks' => $feedbacks,
            'canGiveFeedback' => $canGiveFeedback,
            'contactVerified' => $contactVerified,
        ]);
    }

    public function create(Request $request): Response
{
    $user = $request->user();

    if (!$user) {
        return redirect()->route('login')->with('error', 'Silakan login untuk memberikan feedback.');
    }

    if (!$user->email_verified_at && !$user->no_hp_verified_at) {
        return redirect()->route('feedback.index')->with('error', 'Akun kamu belum diverifikasi. Silakan verifikasi email atau nomor HP terlebih dahulu.');
    }

    if (Feedback::where('user_id', $user->id)->exists()) {
        return redirect()->route('feedback.index')->with('error', 'Kamu sudah pernah memberikan feedback.');
    }

    return Inertia::render('Feedback/Create');
}


public function store(Request $request)
{
    $user = $request->user();

    if (!$user) {
        return redirect()->route('login')->with('error', 'Silakan login untuk memberikan feedback.');
    }

    if (!$user->email_verified_at && !$user->no_hp_verified_at) {
        return redirect()->route('feedback.index')->with('error', 'Akun kamu belum diverifikasi. Silakan verifikasi email atau nomor HP terlebih dahulu.');
    }

    if (Feedback::where('user_id', $user->id)->exists()) {
        return redirect()->route('feedback.index')->with('error', 'Kamu hanya bisa memberikan feedback satu kali.');
    }

    $request->validate([
        'message' => 'required|string|max:5000',
        'rating' => 'required|integer|min:1|max:5',
    ]);

    Feedback::create([
        'user_id' => $user->id,
        'message' => $request->message,
        'rating' => $request->rating,
    ]);

    return redirect()->route('feedback.index')->with('success', 'Feedback berhasil dikirim.');
}

    
    public function edit(Request $request, Feedback $feedback): Response
    {
        $this->authorize('update', $feedback);

        return Inertia::render('Feedback/Edit', [
            'feedback' => $feedback
        ]);
    }

    public function update(Request $request, Feedback $feedback)
    {
        $this->authorize('update', $feedback);

        $request->validate([
            'message' => 'required|string|max:5000',
            'rating' => 'required|integer|min:1|max:5',
        ]);

        $feedback->update([
            'message' => $request->message,
            'rating' => $request->rating,
        ]);

        return redirect()->route('feedback.index')->with('success', 'Feedback berhasil diperbarui.');
    }

    public function destroy(Request $request, Feedback $feedback)
    {
        $this->authorize('delete', $feedback);

        $feedback->delete();

        return redirect()->route('feedback.index')->with('success', 'Feedback berhasil dihapus.');
    }
}
    