<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    /**
     * Mengirim pesan balasan (free-form) ke pengguna WhatsApp.
     * Digunakan untuk membalas dalam 24-hour service window.
     *
     * @param string $to Nomor telepon tujuan dengan format internasional (misal: 62812xxxx)
     * @param string $message Isi pesan yang akan dikirim
     * @return bool True jika berhasil, false jika gagal.
     */
    public static function sendReply(string $to, string $message): bool
    {
        // Ambil kredensial dari file .env
        $token = env('WHATSAPP_API_TOKEN');
        $phoneNumberId = env('WHATSAPP_PHONE_NUMBER_ID');

        // Jika salah satu kredensial tidak ada, catat error dan gagalkan proses
        if (!$token || !$phoneNumberId) {
            Log::error('WHATSAPP_SERVICE: Missing API Token or Phone Number ID in .env file.');
            return false;
        }

        // Buat URL endpoint API WhatsApp Cloud
        $url = "https://graph.facebook.com/v19.0/{$phoneNumberId}/messages";

        // Siapkan data payload sesuai format yang diminta WhatsApp API
        $data = [
            'messaging_product' => 'whatsapp',
            'to' => $to,
            'type' => 'text',
            'text' => [
                'preview_url' => false, // Set false agar URL tidak membuat preview
                'body' => $message,
            ],
        ];

        try {
            // Kirim request POST menggunakan Laravel HTTP Client
            $response = Http::withToken($token)->post($url, $data);

            // Periksa apakah request berhasil (status code 2xx)
            if ($response->successful()) {
                Log::info('WHATSAPP_SERVICE: Message sent successfully.', [
                    'to' => $to, 
                    'response' => $response->json()
                ]);
                return true;
            } else {
                // Jika gagal, catat error beserta response dari server
                Log::error('WHATSAPP_SERVICE: Failed to send message.', [
                    'to' => $to,
                    'status_code' => $response->status(),
                    'response_body' => $response->body()
                ]);
                return false;
            }
        } catch (\Exception $e) {
            // Tangkap jika ada error koneksi atau lainnya
            Log::critical('WHATSAPP_SERVICE: Critical error on sending message.', [
                'error_message' => $e->getMessage(),
                'to' => $to
            ]);
            return false;
        }
    }
}