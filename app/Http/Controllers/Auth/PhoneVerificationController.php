<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;
use App\Services\WhatsAppService;

class PhoneVerificationController extends Controller
{
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

            return redirect(url()->previous());
        }

        return back()->withErrors(['code' => 'Kode salah atau sudah kedaluwarsa']);
    }
}
