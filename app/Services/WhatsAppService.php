<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class WhatsAppService
{
    protected static function authHeader()
    {
        $token = config('services.wablas.token');
        $secret = config('services.wablas.secret_key');
        return $token . '.' . $secret;
    }

    public static function sendVerificationCode(string $phoneNumber, string $code): ?array
    {
        $cleanPhone = preg_replace('/[^0-9]/', '', $phoneNumber);

        $message = "Halo! Berikut adalah kode verifikasi akun Anda di Lapor Pak: {$code}. Jangan berikan kode ini kepada siapa pun. Kode ini berlaku selama 5 menit.";

        $response = Http::withHeaders([
            'Authorization' => self::authHeader(),
        ])->asForm()->post(config('services.wablas.base_url') . '/api/send-message', [
            'phone' => $cleanPhone,
            'message' => $message,
            'secret' => false,
            'priority' => 1,
        ]);
        return $response->json();
    }

    public static function sendMessage(string $phoneNumber, string $message): ?array
    {
        $cleanPhone = preg_replace('/[^0-9]/', '', $phoneNumber);

        $response = Http::withHeaders([
            'Authorization' => self::authHeader(),
        ])->asForm()->post(config('services.wablas.base_url') . '/api/send-message', [
            'phone' => $cleanPhone,
            'message' => $message,
            'secret' => false,
            'priority' => 1,
        ]);
        return $response->json();
    }
}
