<?php

namespace StarEditions\WebhookEvent;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class WebhookEventServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerRoutes();
        $this->registerMigrations();
        $this->registerPublishing();
        $this->loadRoutesFrom(__DIR__.'/routes/api.php');
    }

    protected function registerRoutes()
    {
        Route::group([
            'middleware' => $this->app->configPath('webhook-events-server.middleware'),
            'prefix' => $this->app->configPath('webhook-events-server.path'),
            'namespace' => 'StarEditions\WebhookEvent\Http\Controllers',
            'as' => 'webhook-event.',
        ], function () {
            $this->loadRoutesFrom(__DIR__.'/../routes/api.php');
        });
    }

    /**
     * Register the package migrations.
     *
     * @return void
     */
    protected function registerMigrations()
    {
        if (WebhookEvent::$runsMigrations && $this->app->runningInConsole()) {
            $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        }
    }

    protected function registerPublishing()
    {
        $this->publishes([
            __DIR__.'/../database/migrations' => $this->app->databasePath('migrations'),
        ], 'webhook-migrations');

        $this->publishes([
            __DIR__.'/../config/webhook-events-server.php' => $this->app->configPath('webhook-events-server.php'),
        ], 'webhook-config');
    }
}
