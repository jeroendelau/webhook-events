<?php

namespace StarEditions\WebhookEvent\Tests\Controller;

use Illuminate\Foundation\Auth\User;
use Illuminate\Http\Request;
use StarEditions\WebhookEvent\Http\Requests\WebhookRequest;
use StarEditions\WebhookEvent\Http\Resources\WebhookResource;
use StarEditions\WebhookEvent\Models\Webhook;
use StarEditions\WebhookEvent\Tests\Fakes\UserThatProvidesWebhookOwner;
use StarEditions\WebhookEvent\Tests\Fakes\UserWithWebhookAndCanOverrideScope;
use StarEditions\WebhookEvent\Tests\Fakes\UserWithWebhooks;

class WebhookControllerTest extends AbstractControllerTest
{
    /**
     * POST TESTS
     * -------------
     */
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

    /**
     * GET TESTS
     * -------------
     */


    public function testReturnsAListOfWebhooks(){
        $user = new UserWithWebhooks();
        Webhook::factory()->count(8)->create(['owner_id'=>'1', 'owner_type' => 'StarEditions\WebhookEvent\Tests\Fakes\UserWithWebhooks']);

        $response = $this->actingAs($user)
            ->get('/webhook', ["Accepts" => "application/json"]);

        $response
            ->assertStatus(200)
            ->assertJsonCount(8, "data");
    }

    public function testOnlyReturnsWebhooksForOwner(){
        $user = new UserWithWebhooks();
        Webhook::factory()->count(6)->create(['owner_id'=>'1', 'owner_type' => 'StarEditions\WebhookEvent\Tests\Fakes\UserWithWebhooks']);
        Webhook::factory()->count(5)->create(['owner_id'=>'2', 'owner_type' => 'StarEditions\WebhookEvent\Tests\Fakes\UserWithWebhooks']);

        $response = $this->actingAs($user)
            ->get('/webhook', ["Accepts" => "application/json"]);

        $response
            ->assertStatus(200)
            ->assertJsonCount(6, "data");
    }


    public function testReturnsWebhooksForProvidedOwner(){
        $user = new UserThatProvidesWebhookOwner();
        Webhook::factory()->count(13)->create(['owner_id'=>'3', 'owner_type' => 'StarEditions\WebhookEvent\Tests\Fakes\EntityWithWebhooks']);

        $response = $this->actingAs($user)
            ->get('/webhook', ["Accepts" => "application/json"]);

        $response
            ->assertStatus(200)
            ->assertJsonCount(13, "data");
    }

    # todo: paging etc ...

    /**
     * DELETE TESTS
     * -------------
     */
    public function testDelete(){
        $user = new UserWithWebhooks();
        $wh = Webhook::factory()->createOne(['owner_id'=>$user->id, 'owner_type' => 'StarEditions\WebhookEvent\Tests\Fakes\UserWithWebhooks']);


        $response = $this->actingAs($user)->delete('/webhook/'.$wh->id, ["Accepts" => "application/json"]);

        $response->assertStatus(200);
        $this->assertDatabaseCount( "webhooks", 0);

    }

    public function testCanNotDeleteWebhookThatIsNotOwned(){
        $user = new UserWithWebhooks();
        $wh = Webhook::factory()->createOne(['owner_id'=>"43", 'owner_type' => 'StarEditions\WebhookEvent\Tests\Fakes\UserWithWebhooks']);


        $response = $this->actingAs($user)->delete('/webhook/'.$wh->id, ["Accepts" => "application/json"]);

        $response->assertStatus(200);
        $this->assertDatabaseCount( "webhooks", 1);
    }

    /**
     * GET TESTS
     * -------------
     */
    public function testGet(){
        $user = new UserWithWebhooks();
        $wh = Webhook::factory()->createOne(['owner_id'=>$user->id, 'owner_type' => 'StarEditions\WebhookEvent\Tests\Fakes\UserWithWebhooks']);

        $response = $this->actingAs($user)->get('/webhook/'.$wh->id, ["Accepts" => "application/json"]);

        $result = (new WebhookResource($wh))->toArray(new Request());

        $response
            ->assertStatus(200)
            ->assertJson(["data"=>$result]);

    }

    public function testReturns404IfWebhookIsNotOwnedByRequestor(){
        $user = new UserWithWebhooks();
        $wh = Webhook::factory()->createOne(['owner_id'=>"43", 'owner_type' => 'StarEditions\WebhookEvent\Tests\Fakes\UserWithWebhooks']);

        $response = $this->actingAs($user)->delete('/webhook/'.$wh->id, ["Accepts" => "application/json"]);
        $response->assertStatus(404);
    }

    public function testGetForProvidedWebhookOwner(){
        $user = new UserThatProvidesWebhookOwner();
        $wh = Webhook::factory()->createOne(['owner_id'=>$user->getWebhookOwner()->id, 'owner_type' => 'StarEditions\WebhookEvent\Tests\Fakes\EntityWithWebhooks']);

        $response = $this->actingAs($user)->get('/webhook/'.$wh->id, ["Accepts" => "application/json"]);

        $result = (new WebhookResource($wh))->toArray(new Request());

        $response
            ->assertStatus(200)
            ->assertJson(["data"=>$result]);
    }

    public function testGetReturns404IfNotFound(){
        $user = new UserThatProvidesWebhookOwner();
        $wh = Webhook::factory()->createOne(['owner_id'=>$user->getWebhookOwner()->id, 'owner_type' => 'StarEditions\WebhookEvent\Tests\Fakes\EntityWithWebhooks']);

        $response = $this->actingAs($user)->get('/webhook/12', ["Accepts" => "application/json"]);
        $response->assertStatus(404);
    }
}
