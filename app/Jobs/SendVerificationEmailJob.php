<?php

namespace App\Jobs;

use App\Mail\VerifyEmailLink;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendVerificationEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected string $email;
    protected string $verificationUrl;

    public function __construct(string $email, string $verificationUrl)
    {
        $this->email = $email;
        $this->verificationUrl = $verificationUrl;
    }

    public function handle()
    {
        Mail::to($this->email)->send(new VerifyEmailLink($this->verificationUrl));
    }
}
