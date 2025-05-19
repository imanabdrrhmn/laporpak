<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Carbon\Carbon;

class ResetPasswordController extends Controller
{
    public function reset(Request $request)
    {
        $request->validate([
            'token' => 'required|string',
            'email' => 'required_without:no_hp|nullable|email',
            'no_hp' => 'required_without:email|nullable|string',
            'password' => 'required|min:8|confirmed',
        ]);

        $via = $request->filled('email') ? 'email' : 'phone';
        $identifier = $request->input($via);

        $tokenData = DB::table('password_reset_tokens')
            ->where('identifier', $identifier)
            ->where('via', $via)
            ->first();

        if (!$tokenData || !Hash::check($request->token, $tokenData->token)) {
            return back()->withErrors(['token' => 'Token tidak valid atau sudah kadaluarsa']);
        }

        if (Carbon::now()->gt(Carbon::parse($tokenData->expires_at))) {
            return back()->withErrors(['token' => 'Token telah kedaluwarsa']);
        }

        $user = $via === 'email'
            ? User::where('email', $identifier)->first()
            : User::where('no_hp', $identifier)->first();

        if (!$user) {
            return back()->withErrors(['user' => 'Pengguna tidak ditemukan']);
        }

        $user->password = Hash::make($request->password);
        $user->save();

        DB::table('password_reset_tokens')
            ->where('identifier', $identifier)
            ->where('via', $via)
            ->delete();

        Auth::login($user);

        return redirect()->intended('/dashboard')
            ->with('success', 'Password berhasil direset dan Anda telah login.');
    }
}
