<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ResetPasswordController extends Controller
{
    public function reset(Request $request)
    {
        // Validasi input
        $request->validate([
            'token' => 'required|string',
            'email' => 'nullable|email',
            'no_hp' => 'nullable|string',
            'password' => 'required|min:8|confirmed',
        ]);

        // Cari token di database berdasarkan email atau no_hp
        $tokenData = null;
        if ($request->filled('email')) {
            $tokenData = DB::table('password_reset_tokens')
                ->where('token', $request->token)
                ->where('email', $request->email)
                ->first();
        } elseif ($request->filled('no_hp')) {
            $tokenData = DB::table('password_reset_tokens')
                ->where('token', $request->token)
                ->where('no_hp', $request->no_hp)
                ->first();
        }

        if (!$tokenData) {
            return back()->withErrors(['token' => 'Token tidak valid atau sudah kadaluarsa']);
        }

        // Cek apakah token sudah kedaluwarsa
        if (Carbon::now()->greaterThan(Carbon::parse($tokenData->expires_at))) {
            return back()->withErrors(['token' => 'Token telah kedaluwarsa']);
        }

        // Temukan pengguna berdasarkan email atau no_hp
        $user = null;
        if ($request->filled('email')) {
            $user = User::where('email', $request->email)->first();
        } elseif ($request->filled('no_hp')) {
            $user = User::where('no_hp', $request->no_hp)->first();
        }

        if (!$user) {
            return back()->withErrors(['user' => 'Pengguna tidak ditemukan']);
        }

        // Update password pengguna
        $user->password = Hash::make($request->password);
        $user->save();

        // Hapus token dari database
        DB::table('password_reset_tokens')
            ->where('token', $request->token)
            ->delete();

        // Login pengguna otomatis setelah reset password
        Auth::login($user);

        return redirect(url()->previous());
    }
}
