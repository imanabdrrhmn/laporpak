<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TopUpRejectedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $topUp;

    /**
     * Create a new message instance.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\TopUp  $topUp
     * @return void
     */
    public function __construct($user, $topUp)
    {
        $this->user = $user;
        $this->topUp = $topUp;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('emails.topup.rejected')
                    ->subject('Top Up Ditolak');
    }
}
