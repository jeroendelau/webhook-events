<?php

namespace StarEditions\WebhookEvent\Tests;

use Orchestra\Testbench\TestCase;
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
     * @param \Illuminate\Routing\Router $router
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

        $app['config']->set('webhook-server.queue', 'default');
        $app['config']->set('webhook-server.connection', null);
        $app['config']->set('webhook-server.http_verb', 'post');
        $app['config']->set('webhook-server.signer', \Spatie\WebhookServer\Signer\DefaultSigner::class);
        $app['config']->set('webhook-server.signature_header_name', 'Signature');
        $app['config']->set('webhook-server.headers', [
            'Content-Type' => 'application/json',
        ]);
        $app['config']->set('webhook-server.timeout_in_seconds', 3);
        $app['config']->set('webhook-server.tries', 3);
        $app['config']->set('webhook-server.backoff_strategy', \Spatie\WebhookServer\BackoffStrategy\ExponentialBackoffStrategy::class);
        $app['config']->set('webhook-server.verify_ssl', true);
        $app['config']->set('webhook-server.throw_exception_on_failure', false);
        $app['config']->set('webhook-server.tags', []);
    }
}
