<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use Illuminate\Queue\Events\JobProcessed;
use Illuminate\Queue\Events\JobFailed;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Log;
class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application servi/////////ces.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void

    
    {



         Queue::after(function (JobProcessed $event) {
        Log::info("✅ Job finished: {$event->job->resolveName()}");
    });
            Paginator::useBootstrap();
        //
    }
}
