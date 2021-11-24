<?php

namespace StarEditions\WebhookEvent\Tests\Feature;

use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Event;
use Spatie\WebhookServer\CallWebhookJob;
use StarEditions\WebhookEvent\Facades\WebhookEvent;
use StarEditions\WebhookEvent\Models\Webhook;
use StarEditions\WebhookEvent\Tests\IntegrationTest;

class DispatchTest extends IntegrationTest
{
    public function testDispatchRequiresScope()
    {
        $this->expectException(\Exception::class);
        $this->seedRegularListeners();

        WebhookEvent::create()
            ->topic("test/event")
            ->payload(["foo" => "bar"])
            ->dispatch();

    }

    public function testDispatchRequiresValidTopic()
    {
        $this->expectException(\Exception::class);
        $this->seedRegularListeners();

        WebhookEvent::create()
            ->topic("test/notantevent")
            ->scope("entitywithwebhook.3")
            ->payload(["foo" => "bar"])
            ->dispatch();

    }

    public function testDispatchesToListener()
    {
        $this->seedRegularListeners();

        Bus::fake();

        WebhookEvent::create()
            ->topic("test/event")
            ->scope("entitywithwebhook.3")
            ->payload(["foo" => "bar"])
            ->dispatch();

        Bus::assertDispatched(CallWebhookJob::class);
        $this->assertDatabaseCount(1, "webhook_dispatches");
    }

    public function testDispatchesToMultipleListener()
    {
        $this->seedRegularListeners();
        $this->seedRegularListeners();

        Bus::fake();

        WebhookEvent::create()
            ->topic("test/event")
            ->scope("entitywithwebhook.3")
            ->payload(["foo" => "bar"])
            ->dispatch();

        Bus::assertDispatched(CallWebhookJob::class);
        $this->assertDatabaseCount(2, "webhook_dispatches");
    }

    public function testDispatchesToWildCardScope()
    {
        $this->seedWithScope("*");

        Bus::fake();

        WebhookEvent::create()
            ->topic("test/event")
            ->scope("*")
            ->payload(["foo" => "bar"])
            ->dispatch();

        Bus::assertDispatched(CallWebhookJob::class);
        $this->assertDatabaseCount(1, "webhook_dispatches");
    }

    public function testDispatchesToPartialScope()
    {
        $this->seedWithScope("shop.*");

        Bus::fake();

        WebhookEvent::create()
            ->topic("test/event")
            ->scope("shop.*")
            ->payload(["foo" => "bar"])
            ->dispatch();

        Bus::assertDispatched(CallWebhookJob::class);
        $this->assertDatabaseCount(1, "webhook_dispatches");
    }

    protected function seedRegularListeners()
    {
        Webhook::factory()->createOne([
            'owner_id' => "2",
            'owner_type' => 'StarEditions\WebhookEvent\Tests\Fakes\EntityWithWebhook',
            'topic' => 'test/event',
            'scope' => 'entitywithwebhook.3',
        ]);

        Webhook::factory()->createOne([
            'owner_id' => "4",
            'owner_type' => 'StarEditions\WebhookEvent\Tests\Fakes\UserWithWebhookAndCanOverrideScope',
            'topic' => 'test/event',
            'scope' => 'entitywithwebhook.*',
        ]);
    }

    protected function seedEntityListeners()
    {
        Webhook::factory()->createOne([
            'owner_id' => "2",
            'owner_type' => 'StarEditions\WebhookEvent\Tests\Fakes\EntityWithWebhook',
            'topic' => 'test/event',
            'scope' => 'entitywithwebhook.3',
        ]);

        Webhook::factory()->createOne([
            'owner_id' => "4",
            'owner_type' => 'StarEditions\WebhookEvent\Tests\Fakes\UserWithWebhookAndCanOverrideScope',
            'topic' => 'test/event',
            'scope' => 'entitywithwebhook.*',
        ]);
    }

    protected function seedUnlimitedScope()
    {
        $this->seedWithScope('*');
    }

    protected function seedEntityDotScope()
    {
       $this->seedWithScope('shop.*');
    }

    protected function seedWithScope($scope)
    {
        Webhook::factory()->createOne([
            'owner_id' => "2",
            'owner_type' => 'StarEditions\WebhookEvent\Tests\Fakes\EntityWithWebhook',
            'topic' => 'test/event',
            'scope' => 'shop.*',
        ]);
    }
}
