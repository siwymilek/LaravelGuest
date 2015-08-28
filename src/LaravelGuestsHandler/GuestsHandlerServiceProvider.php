<?php

namespace Siwymilek\LaravelGuestsHandler;

use Illuminate\Support\ServiceProvider;

class GuestsHandlerServiceProvider extends ServiceProvider
{

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('Guest', function ($app) {
            return new Guest;
        });
    }

    public function boot()
    {
//        $this->publishes([
//            __DIR__.'/../config/package.php' => config_path('package.php')
//        ], 'config');

        $this->publishes([
            __DIR__.'/../database/migrations/' => database_path('migrations')
        ], 'migrations');

    }
}