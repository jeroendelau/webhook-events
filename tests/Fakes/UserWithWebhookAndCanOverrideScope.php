<?php

namespace StarEditions\WebhookEvent\Tests\Fakes;

use Illuminate\Foundation\Auth\User as BaseUser;
use StarEditions\WebhookEvent\HasWebhooks;
use StarEditions\WebhookEvent\MightOverWriteScope;

class UserWithWebhookAndCanOverrideScope extends BaseUser implements MightOverWriteScope
{
    use HasWebhooks;

    public $id = 2;
    public $override = true;

    public function setOverride($override)
    {
        $this->override = $override;
    }

    public function canOverwriteScope()
    {
        return $this->override;
    }
}
