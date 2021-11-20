<?php

namespace StarEditions\WebhookEvent;

use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use Spatie\WebhookServer\Events\FinalWebhookCallFailedEvent;
use Spatie\WebhookServer\Events\WebhookCallFailedEvent;
use Spatie\WebhookServer\Events\WebhookCallSucceededEvent;
use StarEditions\WebhookEvent\Models\WebhookDeliveryLog;
use StarEditions\WebhookEvent\Models\WebhookDispatch;

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
        $this->registerListeners();
    }

    /**
     * Register the package migrations.
     *
     * @return void
     */
    protected function registerMigrations()
    {
        if (Webhook::$runsMigrations && $this->app->runningInConsole()) {
            $this->loadMigrationsFrom(__DIR__.'/database/migrations');
        }
    }

    protected function registerPublishing()
    {
        if($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/database/migrations' => $this->app->databasePath('migrations'),
            ], 'webhook-migrations');
    
            $this->publishes([
                __DIR__.'/config/webhook-events-server.php' => $this->app->configPath('webhook-events-server.php'),
            ], 'webhook-config');
        }
    }

    protected function registerListeners()
    {
        Event::listen(function(WebhookCallSucceededEvent $event) {
            $dispatch = WebhookDispatch::find($event->meta['dispatch']);
            $count = WebhookDeliveryLog::where('dispatch_event_id', $dispatch->id)->count();
            $dispatch->update([
                'last_attempt' => now(),
                'success' => 1,
                'attempts' => $count+1
            ]);
            WebhookDeliveryLog::create([
                'webhook_event_id' => $dispatch->id,
                'response_status' => $event->response->getStatusCode(),
                'response_message' => $event->response->getBody()->getContents(),
                'sent_at' => now()
            ]);
        });
        Event::listen(function(WebhookCallFailedEvent $event) {
            $dispatch = WebhookDispatch::find($event->meta['dispatch']);
            WebhookDeliveryLog::create([
                'webhook_event_id' => $dispatch->id,
                'response_status' => $event->response->getStatusCode(),
                'response_message' => $event->response->getBody()->getContents(),
                'sent_at' => now()
            ]);
        });
        Event::listen(function(FinalWebhookCallFailedEvent $event) {
            $dispatch = WebhookDispatch::find($event->meta['dispatch']);
            $count = WebhookDeliveryLog::where('dispatch_event_id', $dispatch->id)->count();
            $dispatch->update([
                'last_attempt' => now(),
                'success' => 0,
                'attempts' => $count+1
            ]);
            WebhookDeliveryLog::create([
                'webhook_event_id' => $dispatch->id,
                'response_status' => $event->response->getStatusCode(),
                'response_message' => $event->response->getBody()->getContents(),
                'sent_at' => now()
            ]);
        });
    }
}
