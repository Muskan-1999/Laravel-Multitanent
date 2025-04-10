<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;

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
        foreach (config('tenancy.central_domains') as $domain) {
            Route::domain($domain)->group(function () use ($domain) {
                Route::middleware('web')
                    ->group(base_path('routes/web.php'));
            });
        }
    }
}
