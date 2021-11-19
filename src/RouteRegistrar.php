<?php

namespace StarEditions\WebhookEvent;

use Illuminate\Contracts\Routing\Registrar as Router;

class RouteRegistrar
{
    protected $router;

    public function __construct(Router $router)
    {
        $this->router = $router;
    }

    public function all()
    {
        $this->forWebhooks();
        $this->forWebhookEvents();
    }

    public function forWebhooks()
    {
        $this->router->resource('webhook', 'WebhookController')
        ->only(['index', 'show', 'store', 'update', 'destroy']);
    }

    public function forWebhookEvents()
    {
        $this->router->get('webhook/event', [
            'uses' => 'WebhookEventController@index',
            'as' => 'webhook-event.index'
        ]);
        $this->router->get('webhook/event/{webhook_event}', [
            'uses' => 'WebhookEventController@show',
            'as' => 'webhook-event.show'
        ]);
        $this->router->get('webhook/event/{webhook_event}/log', [
            'uses' => 'WebhookEventController@logs',
            'as' => 'webhook-event.logs'
        ]);
    }
}
