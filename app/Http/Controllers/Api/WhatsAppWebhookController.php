<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use App\Services\WhatsAppService;
use Exception;

class WhatsAppWebhookController extends Controller
{
    public function handle(Request $request)
        {
        $verifyToken = env('WHATSAPP_VERIFY_TOKEN'); 

        $mode = $request->query('hub_mode');
        $token = $request->query('hub_verify_token');
        $challenge = $request->query('hub_challenge');

        if ($mode === 'subscribe' && $token === $verifyToken) {
            // Respond with the challenge token
            return response($challenge, 200);
        } else {
            return response('Verification failed', 403);
        }
    }
    public function handleIncomingMessage(Request $request)
    {
        Log::info('NEW_MESSAGE: Menerima pesan baru.', ['payload' => $request->all()]);

        try {
            $messageObject = $request->input('entry.0.changes.0.value.messages.0');

            if (!$messageObject || !isset($messageObject['type']) || $messageObject['type'] !== 'text') {
                return response('OK - Not a text message payload.', 200);
            }

            $from = $messageObject['from'];
            $messageText = $messageObject['text']['body'];

            if (!str_contains($messageText, 'WAV-')) {
                return response('OK - Ignored, no trigger token.', 200);
            }

            $triggerToken = substr($messageText, strpos($messageText, 'WAV-'));
            $trigger = DB::table('whatsapp_trigger_tokens')->where('token', $triggerToken)->first();

            if (!$trigger || $trigger->used_at || now()->gt($trigger->expires_at)) {
                Log::warning('OTP_PROCESS: Percobaan menggunakan token tidak valid/kadaluarsa.', ['token' => $triggerToken]);
                return response('OK - Invalid or expired token.', 200);
            }
            
            $otp = rand(100000, 999999);
            Cache::put('wa_verify_' . $trigger->user_id, $otp, now()->addMinutes(5));

            $replyMessage = "Kode verifikasi Anda adalah: *$otp*. Jangan berikan kode ini kepada siapapun.";
            WhatsAppService::sendReply($from, $replyMessage);
            
            DB::table('whatsapp_trigger_tokens')->where('id', $trigger->id)->update(['used_at' => now()]);
            
            return response('OK', 200);

        } catch (Exception $e) {
            Log::critical('WEBHOOK_ERROR: Terjadi error kritis.', [
                'error_message' => $e->getMessage()
            ]);
            return response('Internal Server Error', 500);
        }
    }
       private function processPasswordReset($from, $messageText)
    {
        $triggerToken = substr($messageText, strpos($messageText, 'RESET-'));
        $trigger = DB::table('whatsapp_trigger_tokens')->where('token', $triggerToken)->first();

        if (!$trigger || $trigger->used_at || now()->gt($trigger->expires_at)) {
            WhatsAppService::sendReply($from, 'Maaf, link reset password ini tidak valid atau sudah kedaluwarsa. Silakan coba lagi dari halaman Lupa Password.');
            return response('OK - Invalid trigger token', 200);
        }

        $user = User::find($trigger->user_id);
        if (!$user) {
            return response('OK - User not found', 200);
        }

        $rawToken = Str::random(60);
        $hashedToken = Hash::make($rawToken);

        DB::table('password_reset_tokens')->updateOrInsert(
            ['identifier' => $user->no_hp, 'via' => 'phone'],
            [
                'token' => $hashedToken,
                'created_at' => Carbon::now(),
                'expires_at' => Carbon::now()->addMinutes(10)
            ]
        );

        $resetLink = url("/reset-password?no_hp={$user->no_hp}&token={$rawToken}");

        $replyMessage = "Halo {$user->name}, klik link berikut untuk mereset password Anda (berlaku 10 menit):\n\n{$resetLink}";
        WhatsAppService::sendReply($from, $replyMessage);
        
        DB::table('whatsapp_trigger_tokens')->where('id', $trigger->id)->update(['used_at' => now()]);

        return response('OK', 200);
    }
}

