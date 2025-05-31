<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ReportRejectedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $report;

    public function __construct($user, $report)
    {
        $this->user = $user;
        $this->report = $report;
    }

    public function build()
    {
        return $this->markdown('emails.reports.rejected')
                    ->subject('Laporan Anda Ditolak');
    }
}
