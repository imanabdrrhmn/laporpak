<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use App\Models\User;
use Carbon\Carbon;

class ResetPasswordWithPhoneController extends Controller
{
    /**
     * Show the reset form via no_hp + OTP
     */
    public function showResetForm()
    {
        return inertia('Auth/ResetPasswordPhone');
    }

    /**
     * Handle the password reset using OTP
     */
    public function reset(Request $request)
    {
        $request->validate([
            'no_hp' => 'required|string',
            'token' => 'required|string',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $resetToken = DB::table('password_reset_tokens')
            ->where('no_hp', $request->no_hp)
            ->where('token', $request->token)
            ->first();

        if (! $resetToken || Carbon::parse($resetToken->created_at)->addMinutes(15)->isPast()) {
            return back()->withErrors([
                'token' => 'Token tidak valid atau sudah kedaluwarsa.',
            ]);
        }

        $user = User::where('no_hp', $request->no_hp)->first();

        if (! $user) {
            return back()->withErrors(['no_hp' => 'Pengguna dengan nomor tersebut tidak ditemukan.']);
        }

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        DB::table('password_reset_tokens')->where('no_hp', $request->no_hp)->delete();

        return redirect()->route('login')->with('status', 'Password berhasil diubah.');
    }
}
