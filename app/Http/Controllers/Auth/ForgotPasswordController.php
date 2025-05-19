<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Jobs\SendResetPasswordEmail;
use App\Jobs\SendResetPasswordWhatsApp;

class ForgotPasswordController extends Controller
{
    public function sendResetLink(Request $request)
    {
        $request->validate([
            'email' => 'required_without:no_hp|nullable|email|exists:users,email',
            'no_hp' => 'required_without:email|nullable|string|exists:users,no_hp',
        ]);

        $via = $request->filled('email') ? 'email' : 'phone';
        $identifier = $request->input($via);

        $user = $via === 'email'
            ? User::where('email', $identifier)->first()
            : User::where('no_hp', $identifier)->first();

        if (!$user) {
            return response()->json(['error' => 'Pengguna tidak ditemukan.'], 404);
        }

        $rawToken = Str::random(60);
        $hashedToken = Hash::make($rawToken);

        DB::table('password_reset_tokens')->updateOrInsert(
            ['identifier' => $identifier, 'via' => $via],
            [
                'token' => $hashedToken,
                'created_at' => Carbon::now(),
                'expires_at' => Carbon::now()->addMinutes(10)
            ]
        );

        if ($via === 'email') {
            SendResetPasswordEmail::dispatch($user, $rawToken);
        } else {
            SendResetPasswordWhatsApp::dispatch($user, $rawToken);
        }
        return redirect()->back()->with('message', 'Link reset password berhasil dikirim.');
    }
}
