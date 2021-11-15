<?php

namespace StarEditions\WebhookEvent;

/**
 * 
 */
trait HasWebhooks
{
    protected $apiSecret = '';

    public function getWebhookScope()
    {
        return "store.{$this->id}";
    }

    public function setWebhookSigningSecret($apiSecret)
    {
        $this->apiSecret = $apiSecret;
    }

    public function getWebhookSigningSecret(){
        return $this->apiSecret;
    }
}
