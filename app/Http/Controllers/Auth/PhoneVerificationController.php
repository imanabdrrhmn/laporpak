<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;
use App\Services\WhatsAppService;

class PhoneVerificationController extends Controller
{
    public function send()
    {
        $user = Auth::user();
        $code = rand(100000, 999999);

        // Simpan OTP ke cache selama 5 menit
        Cache::put('wa_verify_' . $user->id, $code, now()->addMinutes(5));

        // Kirim kode via WhatsApp
        $result = WhatsAppService::sendVerificationCode($user->no_hp, $code);

        // Cek apakah pengiriman berhasil
        if ($result && ($result['status'] ?? false)) {
            return redirect()->route('verification.phone.notice')
                ->with('success', 'Kode verifikasi berhasil dikirim ke WhatsApp.');
        }

        return back()->with('error', 'Gagal mengirim kode verifikasi ke WhatsApp.');
    }

    public function verify(Request $request)
    {
        $request->validate([
            'code' => 'required|numeric',
        ]);

        $user = Auth::user();
        $cachedCode = Cache::get('wa_verify_' . $user->id);

        if ($request->code == $cachedCode) {
            $user->markPhoneAsVerified();
            Cache::forget('wa_verify_' . $user->id);

            return redirect()->route('dashboard')->with('success', 'Nomor HP berhasil diverifikasi.');
        }

        return back()->withErrors(['code' => 'Kode salah atau sudah kedaluwarsa']);
    }
}
