<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

use App\Models\Feedback;
use App\Policies\FeedbackPolicy;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        Feedback::class => FeedbackPolicy::class,
    ];

    public function boot(): void
    {
        //
    }
}
