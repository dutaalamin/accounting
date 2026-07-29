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
        // Auto-login for local development
        if (\App\Models\User::first()) {
            \Illuminate\Support\Facades\Auth::login(\App\Models\User::first());
        }
    }
}
