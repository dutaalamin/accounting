<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
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
        if (config('app.env') === 'production') {
            \Illuminate\Support\Facades\URL::forceScheme('https');
        }

        // Auto-login for local development
        if (! $this->app->runningInConsole() && \Illuminate\Support\Facades\Schema::hasTable('users')) {
            if (\App\Models\User::first()) {
                \Illuminate\Support\Facades\Auth::login(\App\Models\User::first());
            }
        }
    }
}
