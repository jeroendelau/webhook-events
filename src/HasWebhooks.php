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
        return "{$this->getModelName()}.{$this->id}";
    }

    private function getModelName()
    {
        $className = get_class($this);
        $className = explode('\\', $className);
        $modelName = $className[count($className) - 1];
        return $modelName;
    }

    public function setWebhookSigningSecret($apiSecret)
    {
        $this->apiSecret = $apiSecret;
    }

    public function getWebhookSigningSecret(){
        return $this->apiSecret;
    }
}
