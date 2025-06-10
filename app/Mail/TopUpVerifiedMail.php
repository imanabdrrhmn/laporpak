<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TopUpVerifiedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $topUp;
    public $logoBase64;

    public function __construct($user, $topUp)
    {
        $this->user = $user;
        $this->topUp = $topUp;
        
        // Convert logo to base64
        $this->logoBase64 = $this->getLogoBase64();
    }

    public function build()
    {
        return $this->markdown('emails.topup.verified')
                    ->subject('Top Up Berhasil - LaporPak.com');
    }

    private function getLogoBase64()
    {
        $logoPath = public_path('images/Logo-LaporPak.svg');
        
        if (file_exists($logoPath)) {
            $logoData = file_get_contents($logoPath);
            return 'data:image/svg+xml;base64,' . base64_encode($logoData);
        }
        
        // Fallback jika file tidak ditemukan
        return null;
    }
}