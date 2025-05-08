<?php

namespace App\Http\Controllers\Feedback;

use App\Models\Feedback;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use App\Http\Controllers\Controller;
use App\Policies\FeedbackPolicy;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;


class FeedbackController extends Controller
{
    use AuthorizesRequests;

    public function index(Request $request): Response
    {
        $user = $request->user();
        $contactVerified = $user && ($user->email_verified_at || $user->no_hp_verified_at);
        $feedbacks = Feedback::with('user')->latest()->get()->map(function ($feedback) {
            $isUpdated = $feedback->updated_at->gt($feedback->created_at);
            return [
                'id' => $feedback->id,
                'user_id' => $feedback->user_id,
                'user' => [
                    'id' => $feedback->user->id,
                    'name' => $feedback->user->name,
                ],
                'message' => $feedback->message,
                'rating' => $feedback->rating,
                'kategori' => $feedback->kategori,
                'created_at' => $feedback->created_at->diffForHumans(), 
                'updated_at' => $isUpdated ? $feedback->updated_at->diffForHumans() : null,
                'is_updated' => $isUpdated,
            ];
        });
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

        // Check if feedback for the category already exists for this user
        $existingFeedback = Feedback::where('user_id', $user->id)
                                    ->where('kategori', $request->kategori)
                                    ->first();

        if ($existingFeedback) {
            return redirect()->route('feedback.index')->with('error', 'Kamu sudah pernah memberikan feedback untuk kategori ini.');
        }

        $request->validate([
            'message' => 'required|string|max:5000',
            'rating' => 'required|integer|min:1|max:5',
            'kategori' => 'required|string|max:255',
        ]);

        Feedback::create([
            'user_id' => $user->id,
            'message' => $request->message,
            'rating' => $request->rating,
            'kategori' => $request->kategori,
        ]);

        return redirect()->route('feedback.index')->with('success', 'Feedback berhasil dikirim.');
    }

    public function edit(Request $request, Feedback $feedback): Response
    {
        // Use the policy to check if the user can update the feedback
        $this->authorize('update', $feedback);

        return Inertia::render('Feedback/Edit', [
            'feedback' => $feedback
        ]);
    }

    public function update(Request $request, Feedback $feedback)
    {
        // Use the policy to check if the user can update the feedback
        $this->authorize('update', $feedback);

        $request->validate([
            'message' => 'required|string|max:5000',
            'rating' => 'required|integer|min:1|max:5',
            'kategori' => 'required|string|max:255',
        ]);

        $feedback->update([
            'message' => $request->message,
            'rating' => $request->rating,
            'kategori' => $request->kategori,
        ]);

        return redirect()->route('feedback.index')->with('success', 'Feedback berhasil diperbarui.');
    }

    public function destroy(Request $request, Feedback $feedback)
    {
        // Use the policy to check if the user can delete the feedback
        $this->authorize('delete', $feedback);

        $feedback->delete();

        return redirect()->route('feedback.index')->with('success', 'Feedback berhasil dihapus.');
    }
}
