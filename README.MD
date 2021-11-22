# Introduction
Systems need to fire events to allow them to be decoupled. To support this event passing we will build a reusable event system that allows services to communicate through webhooks.

We will heavily borrow from the Shopify System to build our own system as it seems robust. And works well for us: https://shopify.dev/apps/webhooks/configuration-overview

The system supports:
- Publishing a list of available webhooks
- Allowing external systems to subscribe to webhooks
- Registering callback urls, and validate proper response
- Firing events to listening webhooks
- Retry webhooks in given intervals
- Log webhooks attempts and results
- Access and Scope controls

#Installation
This is a composer package. We will will develop locally before adding it to it’s own git repository:
```json
// composer.json

"repositories": [
    ...
    {
      "type": "path",
      "url": "./stareditions/webhook-event-server"
    }
]
```


And run:
```shell
composer require stareditions/webhook-event-server
```


And publish the service provider
```shell
 php artisan vendor:publish --provider="StarEditions\WebhookEvent\WebhookEventServiceProvider"
```
This will publish all required services, configurations and migrations.

# Basic Use
Enable Users to have webhooks by adding a trait
```php
    class User {
        use HasWebhooks;
        ...
    }
```

Add a couple of topics to the `config/webhook-events-server.php`
```php
return [
    /*
     *  A list of topics that can be registered
     */
    'topics' => [
        'orders/update',
        "orders/create"
    ],
];
```

Register the routes, make sure it is encapsulated in middleware
that provides an Authenticated users if available.
```php
Route::middleware('auth:sanctum')->group(function () {
    ...
    Webhook::routes();
});
```

Users can register webhooks with the system and start receiving events.
```php
POST https://mygroovyserer/api/webhook
{
    "webhook": {
        "topic": "orders/update",
        "address": "https://12345.ngrok.io/"
    }
}
```

A POST request is sent to the receiving address. We expect to receive a 200 response to confirm the URL, and add the callback url.
We can now start sending events to all webhooks registered for the topic.
```php
WebhookEvent::create()
    ->topic("orders/update")
    ->payload($payload)
    ->dispatch();
```

To send the webhooks only to a given users use:
```php
    WebhookEvent::create()
    ->topic("orders/update")
    ->payload($payload)
    ->scope($user->getWebhookScope())
    ->dispatch();
```

If the endpoint returns a response other than 200, the webhook will be retried. The defaults are set to 5 times, with a progressive backoff. A log of all delivery attempts is kept. For debugging and verification.
