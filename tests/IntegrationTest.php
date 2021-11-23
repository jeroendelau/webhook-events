<?php

namespace Stareditions\WebhookEvent\Tests;

use Orchestra\Testbench\TestCase;
use StarEditions\WebhookEvent\Facades\WebhookEvent;
use StarEditions\WebhookEvent\RouteRegistrar;
use StarEditions\WebhookEvent\Webhook;

abstract class IntegrationTest extends TestCase
{
    /**
     * Setup the test case.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->loadLaravelMigrations(['--database' => 'testbench']);
        $this->artisan('migrate', ['--database' => 'testbench'])->run();
    }

    /**
     * Tear down the test case.
     *
     * @return void
     */
    protected function tearDown(): void
    {
        parent::tearDown();
    }

    /**
     * Get the service providers for the package.
     *
     * @param \Illuminate\Foundation\Application $app
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return ['StarEditions\WebhookEvent\WebhookEventServiceProvider'];
    }

    /**
     * Define routes setup.
     *
     * @param  \Illuminate\Routing\Router  $router
     *
     * @return void
     */
    protected function defineRoutes($router)
    {
        Webhook::routes();
    }

    /**
     * Configure the environment.
     *
     * @param \Illuminate\Foundation\Application $app
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('database.default', 'testbench');

        $app['config']->set('database.connections.testbench', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);

        $app['config']->set('webhook-events-server.topics', [
            "test/event",
            "test/event2"
        ]);
    }
}
