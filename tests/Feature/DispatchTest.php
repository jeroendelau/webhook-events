<?php

namespace StarEditions\WebhookEvent\Tests\Feature;

use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Http;
use Spatie\WebhookServer\CallWebhookJob;
use StarEditions\WebhookEvent\Facades\WebhookEvent;
use StarEditions\WebhookEvent\Models\Webhook;
use StarEditions\WebhookEvent\Models\WebhookDeliveryLog;
use StarEditions\WebhookEvent\Models\WebhookDispatch;
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
        $this->assertDatabaseCount("webhook_dispatches", 1);
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
        $this->assertDatabaseCount("webhook_dispatches", 2);
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
        $this->assertDatabaseCount("webhook_dispatches", 1);
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
        $this->assertDatabaseCount("webhook_dispatches", 1);
    }

    public function testCapturesFailure()
    {
        $this->seedWithScope("shop.*");


        //Bus::fake();
        $this->testClient->letEveryRequestFail();

        WebhookEvent::create()
            ->topic("test/event")
            ->scope("shop.*")
            ->payload(["foo" => "bar"])
            ->dispatch();


        //Bus::assertDispatched(CallWebhookJob::class);
        $this->assertDatabaseCount("webhook_delivery_logs", 1);

        $this->assertEquals(WebhookDeliveryLog::first()->response_status, 500 );
        $dispatch = (WebhookDispatch::first());
        $this->assertEquals(1, $dispatch->attempts);
        $this->assertFalse($dispatch->success);
    }

    public function testCapturesSuccess()
    {
        $this->seedWithScope("shop.*");


        //Bus::fake();

        WebhookEvent::create()
            ->topic("test/event")
            ->scope("shop.*")
            ->payload(["foo" => "bar"])
            ->dispatch();


        //Bus::assertDispatched(CallWebhookJob::class);
        $this->assertDatabaseCount("webhook_delivery_logs", 1);

        $this->assertEquals(200, WebhookDeliveryLog::first()->response_status );

        $dispatch = (WebhookDispatch::first());
        $this->assertEquals(1, $dispatch->attempts);
        $this->assertTrue($dispatch->success);
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
