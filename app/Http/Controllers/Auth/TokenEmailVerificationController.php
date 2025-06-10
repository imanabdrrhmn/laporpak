<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\EmailVerificationToken;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\RedirectResponse;
use App\Jobs\SendVerificationEmailJob;

class TokenEmailVerificationController extends Controller
{
    /**
     * Kirim email verifikasi dengan token unik.
     */
    public function send(Request $request): RedirectResponse
    {
        $user = $request->user();

        if ($user->hasVerifiedEmail()) {
            return redirect()->intended(route('dashboard', absolute: false));
        }

        $token = Str::random(64);

        EmailVerificationToken::updateOrCreate(
            ['user_id' => $user->id],
            [
                'token' => $token,
                'expires_at' => now()->addMinutes(5),
            ]
        );

        $url = route('email.verify.token', ['token' => $token]);

        SendVerificationEmailJob::dispatch($user->email, $url);

        return back()->with([
            'status' => 'verification-link-sent',
            'message' => 'Link verifikasi telah dikirim ke email Anda. Silakan periksa inbox Anda.',
            'type' => 'success'
        ]);
    }

    /**
     * Verifikasi email menggunakan token.
     */
    public function verify(Request $request): RedirectResponse
    {
        $token = $request->query('token');

        $record = EmailVerificationToken::where('token', $token)->first();

        if (!$record || $record->isExpired()) {
            return redirect('/')
                ->with([
                    'error' => 'Token tidak valid atau telah kedaluwarsa. Silakan minta link verifikasi baru.',
                    'type' => 'error'
                ]);
        }

        $user = $record->user;

        if (!$user->hasVerifiedEmail()) {
            $user->markEmailAsVerified();
            event(new Verified($user));
        }

        $record->delete();

        return redirect('/')
            ->with([
                'success' => 'Email berhasil diverifikasi! Sekarang Anda dapat mengakses semua fitur aplikasi.',
                // 'message' => 'Selamat! Akun Anda sudah aktif dan siap digunakan.',
                'type' => 'success'
            ]);
    }
}