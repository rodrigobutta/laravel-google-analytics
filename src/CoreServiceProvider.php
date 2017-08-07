<?php

namespace RodrigoButta\LaravelGoogleAnalytics\Core;

use Illuminate\Support\ServiceProvider;

class CoreServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishConfig();
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->registerCore();
    }

    /**
     * Register Google Commands.
     *
     * @return void
     */
    private function registerCore()
    {
        $this->app->singleton(Core::class, function ($app) {
            return new Core(
                config('laravel-google-analytics')
            );
        });
    }

    /**
     * Publish google configuration file.
     *
     * @return void
     */
    private function publishConfig()
    {
        $this->publishes([
            __DIR__.'/config/laravel-google-analytics.php' => config_path('laravel-google-analytics.php'),
        ]);
    }
}
