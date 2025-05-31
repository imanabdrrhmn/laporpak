<?php

namespace App\Jobs;

use App\Mail\ReportUnpublishedMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendReportUnpublishedMailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $user;
    protected $report;

    public function __construct($user, $report)
    {
        $this->user = $user;
        $this->report = $report;
    }

    public function handle()
    {
        Mail::to($this->user->email)->send(new ReportUnpublishedMail($this->user, $this->report));
    }
}
