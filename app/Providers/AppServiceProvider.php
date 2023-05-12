<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use PromoCode\Locator\CurrentAdminLocator;
use PromoCode\Locator\CurrentUserLocator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(CurrentAdminLocator::class);
        $this->app->singleton(CurrentUserLocator::class);
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
