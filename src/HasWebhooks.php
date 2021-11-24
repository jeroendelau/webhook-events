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
        if($this instanceof ProvidesWebhookOwner) {
            $className = get_class($this->getWebhookOwner());
        }else {
            $className = get_class($this);
        }
        
        $className = explode('\\', $className);
        $modelName = $className[count($className) - 1];
        return strtolower($modelName);
    }

    public function setWebhookSigningSecret($apiSecret)
    {
        $this->apiSecret = $apiSecret;
    }

    public function getWebhookSigningSecret(){
        return $this->apiSecret;
    }

    public function webhooks()
    {
        return $this->morphMany(\StarEditions\WebhookEvent\Models\Webhook::class, 'owner');
    }
}
