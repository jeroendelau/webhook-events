<?php

namespace StarEditions\WebhookEvent\Tests\Controller;

use StarEditions\WebhookEvent\Models\Webhook;
use StarEditions\WebhookEvent\Tests\Fakes\UserThatProvidesWebhookOwner;
use StarEditions\WebhookEvent\Tests\Fakes\UserWithWebhookAndCanOverrideScope;
use StarEditions\WebhookEvent\Tests\Fakes\UserWithWebhooks;

class WebhookControllerTest extends AbstractControllerTest
{
    public function testCanAttachToDefaultUser(){
        $response = $this->actingAs(new UserWithWebhooks())
            ->post('/webhook', [
                "url" => "https://www.example.com",
                "topic" => "test/event"
            ], ["Accepts" => "application/json"]);

        $response->assertStatus(200);
    }

    public function testEnabledFlagDefaultsToTrue(){
        $response = $this->actingAs(new UserWithWebhooks())
            ->post('/webhook', [
                "url" => "https://www.example.com",
                "topic" => "test/event"
            ], ["Accepts" => "application/json"]);

        $response->assertStatus(200);

        $this->assertTrue((Webhook::first())->enabled);
    }

    public function testOptionalEnabledFlag(){
        $response = $this->actingAs(new UserWithWebhooks())
            ->post('/webhook', [
                "url" => "https://www.example.com",
                "topic" => "test/event",
                "enabled" => false
            ], ["Accepts" => "application/json"]);

        $response->assertStatus(200);

        $webhook = Webhook::first();
        $this->assertFalse($webhook->enabled);
    }

    public function testScopeIsIgnoredWhenUserCantUpdateScope(){
        $response = $this->actingAs(new UserWithWebhooks())
            ->post('/webhook', [
                "url" => "https://www.example.com",
                "topic" => "test/event",
                "scope" => "myscope"
            ], ["Accepts" => "application/json"]);

        $response->assertStatus(200);

        $this->assertEquals("userwithwebhooks.1",  (Webhook::first())->scope);
    }

    public function testScopeIsSetIfUserCanUpdateScope(){
        $response = $this->actingAs((new UserWithWebhookAndCanOverrideScope()))
            ->post('/webhook', [
                "url" => "https://www.example.com",
                "topic" => "test/event",
                "scope" => "myscope"
            ], ["Accepts" => "application/json"]);

        $response->assertStatus(200);

        $this->assertEquals("myscope",  (Webhook::first())->scope);
    }

    public function testScopeNotSetIfUserCantUpdateScope(){
        $response = $this->actingAs(
            (new UserWithWebhookAndCanOverrideScope())
            ->setCanOverride(false))
            ->post('/webhook', [
                "url" => "https://www.example.com",
                "topic" => "test/event",
                "scope" => "myscope"
            ], ["Accepts" => "application/json"]);

        $response->assertStatus(200);

        $this->assertEquals("userwithwebhookandcanoverridescope.1",  (Webhook::first())->scope);
    }

    public function testScopeForUserThatCanOverrideScopeSetToDefaultIfNotProvided(){
        $response = $this->actingAs((new UserWithWebhookAndCanOverrideScope()))
            ->post('/webhook', [
                "url" => "https://www.example.com",
                "topic" => "test/event",
            ], ["Accepts" => "application/json"]);

        $response->assertStatus(200);

        $this->assertEquals("userwithwebhookandcanoverridescope.2",  (Webhook::first())->scope);
    }

    public function testReturnsWebhookResourceAfterCreation(){
        $response = $this->actingAs(new UserWithWebhooks())
            ->post('/webhook', [
                "url" => "https://www.example.com",
                "topic" => "test/event",
            ], ["Accepts" => "application/json"]);

        $response->assertStatus(200);
        $response->assertJsonFragment(
            [
                "url" => "https://www.example.com",
                "scope" => "userwithwebhooks.1",
                "topic" => "test/event",
                "enabled" => true,
            ]
        );
    }

    public function testAttachToReferredEntity(){
        $user = new UserThatProvidesWebhookOwner();
        $response = $this->actingAs($user)
            ->post('/webhook', [
                "url" => "https://www.example.com",
                "topic" => "test/event",
            ], ["Accepts" => "application/json"]);

        $response->assertStatus(200);

        $this->assertDatabaseCount("webhooks", 1);
        $this->assertCount(1, $user->getWebhookOwner()->webhooks);
        $this->assertEquals("entitywithwebhook.3",  (Webhook::first())->scope);
    }

    public function testFailsIfTopicDoesNotExist(){
        $user = new UserThatProvidesWebhookOwner();
        $response = $this->actingAs($user)
            ->post('/webhook', [
                "url" => "https://www.example.com",
                "topic" => "test/doesntexist",
            ], ["Accepts" => "application/json"]);

        $response->assertStatus(422);
    }

    public function testFailsIfUrlDoesNotReturn200(){
        $user = new UserThatProvidesWebhookOwner();
        $response = $this->actingAs($user)
            ->post('/webhook', [
                "url" => "https://www.idonotexistithink.com",
                "topic" => "test/event",
            ], ["Accepts" => "application/json"]);

        $response->assertStatus(422);
    }
}
