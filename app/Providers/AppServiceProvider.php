<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL; // <--- INI YANG HILANG SEBELUMNYA

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
        // Paksa HTTPS agar tidak error "Not Secure" dan Cookie Login bisa tersimpan
        if($this->app->environment('production') || $this->app->environment('local')) {
            URL::forceScheme('https');
        }
    }
}