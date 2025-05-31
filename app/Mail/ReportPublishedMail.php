<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ReportPublishedMail extends Mailable
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
        return $this->markdown('emails.reports.published')
                    ->subject('Laporan Anda Telah Dipublikasikan');
    }
}
