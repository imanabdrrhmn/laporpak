<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

use App\Models\Feedback;
use App\Policies\FeedbackPolicy;

use App\Models\Report;
use App\Policies\ReportPolicy;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        Feedback::class => FeedbackPolicy::class,
        Report::class => ReportPolicy::class,
    ];

    public function boot(): void
    {
        
    }
}
