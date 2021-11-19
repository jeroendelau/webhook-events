<?php

namespace StarEditions\WebhookEvent\Facades;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Facade;
use Spatie\WebhookServer\WebhookCall;
use StarEditions\WebhookEvent\Models\Webhook;

class WebhookEvent extends Facade
{
    protected $payload;
    protected $topic;
    protected $scope;
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'webhook';
    }

    public static function create(): self
    {
        return new static();
    }

    public function payload(array $payload): self
    {
        $this->payload = $payload;
        return $this;
    }

    public function topic(string $topic): self
    {
        $this->topic = $topic;
        return $this;
    }

    public function scope(string $scope): self
    {
        $this->scope = $scope;
        return $this;
    }

    public function dispatch()
    {
        // $webhooks = Webhook::webhookOwner(Auth::user()->getWebhookOwner())
        // ->topic($this->topic)->get();
        // foreach ($webhooks as $webhook) {
        //     $response = WebhookCall::create()
        //     ->url($webhook->url)
        //     ->payload($this->payload)
        //     ->dispatch();
        // }
    }
}