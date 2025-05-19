<?php

namespace App\Jobs;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Mail;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class SendResetPasswordEmail implements ShouldQueue
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
        $resetLink = url("/reset-password?email={$this->user->email}&token={$this->token}");

        Mail::send('emails.reset_password', [
            'name' => $this->user->name,
            'resetLink' => $resetLink,
        ], function ($message) {
            $message->to($this->user->email)
                    ->subject('Reset Password - LaporPak');
        });
    }
}
