<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use App\Models\User;
use App\Jobs\SendResetPasswordEmail; 
use App\Jobs\SendResetPasswordWhatsApp; 
use Illuminate\Support\Facades\DB;

class ForgotPasswordController extends Controller
{
    public function sendResetLink(Request $request)
    {
        // Validasi input untuk email atau no_hp
        $request->validate([
            'email' => 'required_without:no_hp|nullable|email|exists:users,email',
            'no_hp' => 'required_without:email|nullable|string|exists:users,no_hp',
        ]);

        // Temukan pengguna berdasarkan email atau nomor HP
        $user = null;
        if ($request->filled('email')) {
            $user = User::where('email', $request->email)->first();
        } elseif ($request->filled('no_hp')) {
            $user = User::where('no_hp', $request->no_hp)->first();
        }

        if (!$user) {
            return response()->json(['error' => 'Pengguna tidak ditemukan.'], 404);
        }

        // Generate token untuk reset password
        $token = Str::random(60); // Generate token yang panjang

        // Simpan token di tabel password_reset_tokens dengan waktu kedaluwarsa
        DB::table('password_reset_tokens')->updateOrInsert(
            [
                'email' => $user->email,
                'no_hp' => $user->no_hp,
            ],
            [
                'token' => $token,  // Simpan token
                'created_at' => Carbon::now(),
                'expires_at' => Carbon::now()->addMinutes(10)  // Token berlaku selama 10 menit
            ]
        );

        if ($request->filled('email')) {
            SendResetPasswordEmail::dispatch($user, $token);
        }
        
        if ($request->filled('no_hp')) {
            SendResetPasswordWhatsApp::dispatch($user, $token);
        }
        return response()->json([
            'message' => 'Link reset password telah dikirim ke email Anda. Link ini berlaku selama 10 menit.'
        ], 200);
    }
}
