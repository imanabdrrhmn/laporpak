<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TopUpVerifiedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $topUp;

    public function __construct($user, $topUp)
    {
        $this->user = $user;
        $this->topUp = $topUp;
    }

    public function build()
    {
        return $this->markdown('emails.topup.verified')
                    ->subject('Top Up Berhasil Diverifikasi');
    }
}
