<?php

namespace StarEditions\WebhookEvent\Tests\Fakes;


use Illuminate\Foundation\Auth\User as BaseUser;
use StarEditions\WebhookEvent\ProvidesWebhookOwner;

class UserThatProvidesWebhookOwner extends BaseUser implements ProvidesWebhookOwner
{
    public $id = 3;
    protected $entityWithWebhook;

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->entityWithWebhook = new EntityWithWebhook();
    }

    /**
     * @return mixed
     */
    public function getWebhookOwner()
    {
        return $this->entityWithWebhook();
    }
}
