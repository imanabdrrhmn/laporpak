<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use Carbon\Carbon;

class ForgotPasswordWithPhoneController extends Controller
{
    /**
     * Show the OTP request form (via no_hp)
     */
    public function showRequestForm()
    {
        return inertia('Auth/ForgotPasswordPhone');
    }

    /**
     * Handle OTP sending via phone number
     */
    public function sendOtp(Request $request)
    {
        $request->validate([
            'no_hp' => ['required', 'exists:users,no_hp'],
        ]);

        $otp = random_int(100000, 999999); // bisa pakai str_pad() jika perlu

        DB::table('password_reset_tokens')->updateOrInsert(
            ['no_hp' => $request->no_hp],
            ['token' => $otp, 'created_at' => Carbon::now()]
        );

        // TODO: Kirim OTP via SMS
        // SmsService::send($request->no_hp, "Kode reset kamu: $otp");

        return back()->with('status', 'Kode OTP berhasil dikirim ke nomor Anda.');
    }
}
