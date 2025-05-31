<?php

namespace App\Jobs;

use App\Mail\TopUpRejectedMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendTopUpRejectedMailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $user;
    protected $topUp;

    public function __construct($user, $topUp)
    {
        $this->user = $user;
        $this->topUp = $topUp;
    }

    public function handle()
    {
        Mail::to($this->user->email)
            ->send(new TopUpRejectedMail($this->user, $this->topUp));
    }
}
