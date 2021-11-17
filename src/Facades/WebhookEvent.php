<?php

namespace StarEditions\WebhookEvent\Facades;

use Illuminate\Support\Facades\Facade;

class WebhookEvent extends Facade
{
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
        return $this;
    }

    public function topic(string $topic): self
    {
        return $this;
    }

    public function scope(string $scope): self
    {
        return $this;
    }
}