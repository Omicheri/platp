<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Plat;
use App\Observers\PlatObserver;
class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {

    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Plat::observe(PlatObserver::class);
    }
}
