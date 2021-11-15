<?php

namespace StarEditions\WebhookEvent;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use StarEditions\WebhookEvent\Models\Webhook;
use StarEditions\WebhookEvent\Models\WebhookDeliveryLog;
use StarEditions\WebhookEvent\Policies\WebhookDeliveryLogPolicy;
use StarEditions\WebhookEvent\Policies\WebhookPolicy;

class WebhookEventServiceProvider extends ServiceProvider
{
    protected $policies = [
        Webhook::class => WebhookPolicy::class,
        WebhookDeliveryLog::class => WebhookDeliveryLogPolicy::class
    ];
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
        $this->registerPolicies();
        $this->loadRoutesFrom(__DIR__.'/routes/api.php');
    }

    /**
     * Register the package routes.
     *
     * @return void
     */
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
     * Register the package policies.
     *
     * @return void
     */

    protected function registerPolicies()
    {
        foreach ($this->policies as $key => $value) {
            Gate::policy($key, $value);
        }
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
