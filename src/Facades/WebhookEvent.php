<?php

namespace StarEditions\WebhookEvent\Facades;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Facade;
use Spatie\WebhookServer\WebhookCall;
use StarEditions\WebhookEvent\MightOverWriteScope;
use StarEditions\WebhookEvent\Models\Webhook;
use StarEditions\WebhookEvent\Models\WebhookDispatch;
use StarEditions\WebhookEvent\ProvidesWebhookOwner;

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
        $webhooks = Webhook::where('scope', $this->scope)
        ->topic($this->topic)
        ->where('enabled', 1)
        ->get();

        foreach ($webhooks as $webhook) {
            $dispatch = WebhookDispatch::create([
                'webhook_id' => $webhook->id,
                'topic' => $this->topic,
                'payload' => $this->payload,
            ]);
            $webhookCall = WebhookCall::create()
            ->meta([
                'dispatch' => $dispatch->id
            ])
            ->maximumTries(5);
            if(app()->runningInConsole()) {
                $webhookCall->doNotSign();
            }else {
                $webhookCall->useSecret(Auth::user()->getWebhookSigningSecret());
            }
            $webhookCall->url($webhook->url)
            ->payload($this->payload)
            ->dispatch();
        }
    }
}