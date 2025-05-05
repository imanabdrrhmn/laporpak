<?php

namespace App\Jobs;

use App\Services\WhatsAppService;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class SendResetPasswordWhatsApp 
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $user;
    public $token;

    /**
     * Create a new job instance.
     *
     * @param User $user
     * @param string $token
     */
    public function __construct(User $user, string $token)
    {
        $this->user = $user;
        $this->token = $token;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $message = "Halo! Berikut adalah link untuk reset kata sandi akun Anda: https://localhost:8000/reset-password?token={$this->token}&no_hp={$this->user->no_hp}. Link ini berlaku selama 10 menit.";

        
        WhatsAppService::sendMessage($this->user->no_hp, $message);
    }
}
