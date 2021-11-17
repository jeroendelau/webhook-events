<?php

namespace StarEditions\WebhookEvent;

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
        $this->registerMigrations();
        $this->registerPublishing();
    }

    /**
     * Register the package migrations.
     *
     * @return void
     */
    protected function registerMigrations()
    {
        if (Webhook::$runsMigrations && $this->app->runningInConsole()) {
            $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        }
    }

    protected function registerPublishing()
    {
        if($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../database/migrations' => $this->app->databasePath('migrations'),
            ], 'webhook-migrations');
    
            $this->publishes([
                __DIR__.'/../config/webhook-events-server.php' => $this->app->configPath('webhook-events-server.php'),
            ], 'webhook-config');
        }
    }
}
