<?php

namespace StarEditions\WebhookEvent\Tests\Fakes;

use Illuminate\Foundation\Auth\User as BaseUser;
use StarEditions\WebhookEvent\HasWebhooks;

class UserWithWebhooks extends BaseUser
{
    use HasWebhooks;
    public $id = 1;
}
