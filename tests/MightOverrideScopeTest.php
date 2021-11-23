<?php
namespace StarEditions\WebhookEvents\Tests;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

use Orchestra\Testbench\TestCase;
use StarEditions\WebhookEvent\HasWebhooks;

class MightOverrideScopeTest extends TestCase
{
    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('database.default', 'testbench');

        $app['config']->set('database.connections.testbench', [
            'driver'   => 'sqlite',
            'database' => ':memory:',
            'prefix'   => '',
        ]);
    }

    public function testUserCannotOverrideScope(){
        $this->loadLaravelMigrations(['--database' => 'testbench']);
        $this->artisan('migrate', ['--database' => 'testbench'])->run();


    }

}


class RegularUser extends User
{
    use HasWebhooks;

}
