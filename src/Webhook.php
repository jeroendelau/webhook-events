<?php

namespace StarEditions\WebhookEvent;

use Illuminate\Support\Facades\Route;

class Webhook
{
    public static $runsMigrations = true;

    public static function routes(array $options = [])
    {
        $callback = function ($router) {
            $router->all();
        };

        $defaultOptions = [
            'namespace' => '\StarEditions\WebhookEvent\Http\Controllers',
        ];
        $options = array_merge($defaultOptions, $options);

        Route::group($options, function($router) use ($callback) {
            $callback(new RouteRegistrar($router));
        });
    }
}
