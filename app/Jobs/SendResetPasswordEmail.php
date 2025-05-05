<?php

namespace App\Jobs;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Mail;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class SendResetPasswordEmail
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
        $link = url("/reset-password?token={$this->token}&email={$this->user->email}");

        $emailBody = "
        <html>
        <body>
            <h2>Hello {$this->user->name},</h2>
            <p>We received a request to reset your password. If you did not make this request, you can ignore this email.</p>
            <p>To reset your password, click the link below:</p>
            <p><a href='{$link}' style='background-color:#007bff;color:white;padding:10px 20px;text-decoration:none;'>Reset Password</a></p>
            <p>This link will expire in 10 minutes.</p>
            <p>If you have any questions, feel free to contact us.</p>
            <br>
            <p>Thanks, <br> The Team</p>
        </body>
        </html>
        ";

        // Send the email with the custom HTML content
        Mail::html($emailBody, function ($message) {
            $message->to($this->user->email)
                    ->subject('Password Reset Link');
        });              
    }
}
