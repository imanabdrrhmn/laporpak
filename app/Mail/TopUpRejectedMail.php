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
    public $logoBase64;

    public function __construct($user, $topUp)
    {
        $this->user = $user;
        $this->topUp = $topUp;
        $this->logoBase64 = $this->getLogoBase64();
    }

    public function build()
    {
        return $this->view('emails.topup.rejected')
                    ->subject('Top Up Gagal - LaporPak.com')
                    ->with([
                        'user' => $this->user,
                        'topUp' => $this->topUp,
                        'logoBase64' => $this->logoBase64,
                    ]);
    }

    private function getLogoBase64()
    {
        $logoPath = public_path('images/Logo-LaporPak.png');

        if (file_exists($logoPath)) {
            $mime = mime_content_type($logoPath);
            $logoData = file_get_contents($logoPath);
            return "data:$mime;base64," . base64_encode($logoData);
        }

        return null;
    }
}
