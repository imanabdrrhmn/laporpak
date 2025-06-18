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
        if ($request->isMethod('get') && $request->has('hub.mode')) {

            $mode = $request->input('hub.mode');
            $token = $request->input('hub.verify_token');
            $challenge = $request->input('hub.challenge');

            $verifyToken = env('WHATSAPP_VERIFY_TOKEN');

            if ($mode === 'subscribe' && $token === $verifyToken) {
                Log::info('WEBHOOK_VERIFICATION: Verifikasi berhasil!');
                return response($challenge, 200);
            } else {
                Log::error('WEBHOOK_VERIFICATION: Verifikasi Gagal. Token tidak cocok atau parameter hilang.');
                return response('Forbidden: Verify token does not match.', 403);
            }
        }

        if ($request->isMethod('post')) {
            Log::info('NEW_MESSAGE: Menerima pesan baru via webhook.', ['payload' => $request->all()]);

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
                    Log::warning('OTP_PROCESS: Percobaan menggunakan token tidak valid, sudah dipakai, atau kedaluwarsa.', ['token' => $triggerToken]);
                    return response('OK - Invalid, used, or expired token.', 200);
                }
                
                $otp = rand(100000, 999999);
                Cache::put('wa_verify_' . $trigger->user_id, $otp, now()->addMinutes(5));

                $replyMessage = "Kode verifikasi Anda adalah: *$otp*. Jangan berikan kode ini kepada siapapun.";
                WhatsAppService::sendReply($from, $replyMessage);
                
                DB::table('whatsapp_trigger_tokens')->where('id', $trigger->id)->update(['used_at' => now()]);
                
            } catch (Exception $e) {
                Log::critical('WEBHOOK_ERROR: Terjadi error kritis.', [
                    'error_message' => $e->getMessage(),
                ]);
                return response('Internal Server Error', 500);
            }
        }
        
        return response('OK', 200);
    }
}