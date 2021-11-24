<?php

namespace StarEditions\WebhookEvent\Tests\Fakes;

use Illuminate\Database\Eloquent\Model;
use StarEditions\WebhookEvent\HasWebhooks;

class EntityWithWebhook extends Model
{
    use HasWebhooks;
    public $id = "3";
}
