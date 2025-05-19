<?php

namespace App\Jobs;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Http;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class SendResetPasswordWhatsApp implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $user;
    protected $token;

    public function __construct(User $user, string $token)
    {
        $this->user = $user;
        $this->token = $token;
    }

    public function handle()
    {
        $link = url("/reset-password?no_hp={$this->user->no_hp}&token={$this->token}");

        $message = "Halo {$this->user->name}, klik link berikut untuk reset password akun Anda (berlaku 10 menit):\n\n{$link}";

        Http::withHeaders([
            'Authorization' => env('WABLAS_API_KEY'),
        ])->post('https://kirim.pesanapi.com/api/v2/send-message', [
            'phone' => $this->user->no_hp,
            'message' => $message,
        ]);
    }
}
