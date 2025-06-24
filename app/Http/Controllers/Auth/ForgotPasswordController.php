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

class ForgotPasswordController extends Controller
{
    public function sendResetLink(Request $request)
    {
        $request->validate([
            'identifier' => 'required|string',
        ]);
        $identifier = $request->input('identifier');

        if (filter_var($identifier, FILTER_VALIDATE_EMAIL)) {
            return $this->handleEmailReset($identifier);
        } else {
            return $this->generateWhatsAppResetTrigger($identifier);
        }
    }

    protected function handleEmailReset($email)
    {
        $user = User::where('email', $email)->first();
        if (!$user) {
            return response()->json(['message' => 'Jika email terdaftar, petunjuk reset telah dikirim.']);
        }

        $rawToken = Str::random(60);
        $hashedToken = Hash::make($rawToken);

        DB::table('password_reset_tokens')->updateOrInsert(
            ['identifier' => $email, 'via' => 'email'],
            [
                'token' => $hashedToken,
                'created_at' => Carbon::now(),
                'expires_at' => Carbon::now()->addMinutes(10)
            ]
        );

        SendResetPasswordEmail::dispatch($user, $rawToken);

        return response()->json(['message' => 'Jika email terdaftar, petunjuk reset telah dikirim.']);
    }


    protected function generateWhatsAppResetTrigger($phoneNumber)
    {
        $user = User::where('no_hp', $phoneNumber)->first();
        if (!$user) {
            return response()->json(['message' => 'Jika nomor HP terdaftar, petunjuk reset akan ditampilkan.']);
        }
        
        $triggerToken = 'RESET-' . Str::random(20);

        DB::table('whatsapp_trigger_tokens')->insert([
            'user_id' => $user->id,
            'token' => $triggerToken,
            'phone_number' => $user->no_hp,
            'expires_at' => now()->addMinutes(10),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        
        return response()->json([
            'type' => 'whatsapp', 
            'trigger_token' => $triggerToken,
            'business_phone_number' => env('WHATSAPP_BUSINESS_NUMBER'),
        ]);
    }
}