<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

use App\Models\Feedback;
use App\Models\TopUp;
use App\Models\Report;
use App\Policies\ReportPolicy;
use App\Policies\TopUpPolicy;
use App\Policies\FeedbackPolicy;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        Feedback::class => FeedbackPolicy::class,
        Report::class => ReportPolicy::class,
        TopUp::class => TopUpPolicy::class,
    ];

    public function boot(): void
    {
        
    }
}
