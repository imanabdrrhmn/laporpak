<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Models\User; 
use Illuminate\Support\Facades\DB;

class WhatsAppTriggerController extends Controller
{
    public function generate(Request $request)
    {
        $user = Auth::user();

        if (!$user->no_hp) {
            return response()->json(['error' => 'Nomor HP tidak ditemukan.'], 400);
        }

        $token = 'WAV-' . Str::random(15);
        $expiresAt = now()->addMinutes(3); 

        DB::table('whatsapp_trigger_tokens')->insert([
            'user_id' => $user->id,
            'token' => $token,
            'phone_number' => $user->no_hp,
            'expires_at' => $expiresAt,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json([
            'token' => $token,
            'phone_number' => env('WHATSAPP_BUSINESS_NUMBER'),
            'expires_at' => $expiresAt->toIso8601String(),
        ]);
    }
}