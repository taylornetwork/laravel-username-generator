<?php

namespace TaylorNetwork\UsernameGenerator\Tests;

use Orchestra\Testbench\TestCase as OrchestraTestCase;
use TaylorNetwork\UsernameGenerator\Facades\UsernameGenerator;
use TaylorNetwork\UsernameGenerator\ServiceProvider;
use TaylorNetwork\UsernameGenerator\Tests\Database\Seeders\CustomColumnUserSeeder;
use TaylorNetwork\UsernameGenerator\Tests\Database\Seeders\CustomFieldUserSeeder;
use TaylorNetwork\UsernameGenerator\Tests\Database\Seeders\EmailUserSeeder;
use TaylorNetwork\UsernameGenerator\Tests\Database\Seeders\UserSeeder;
use TaylorNetwork\UsernameGenerator\Tests\Models\User;

class TestCase extends OrchestraTestCase
{
    protected function getPackageProviders($app)
    {
        return [ServiceProvider::class];
    }

    protected function getPackageAliases($app)
    {
        return ['Gen' => UsernameGenerator::class];
    }

    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('username_generator.model', User::class);

        // Setup default database to use sqlite :memory:
        $app['config']->set('database.default', 'testbench');
        $app['config']->set('database.connections.testbench', [
            'driver'   => 'sqlite',
            'database' => ':memory:',
            'prefix'   => '',
        ]);
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->loadMigrationsFrom(implode(DIRECTORY_SEPARATOR, [__DIR__, 'Database', 'Migrations']));
        $this->seed(UserSeeder::class);
        $this->seed(EmailUserSeeder::class);
        $this->seed(CustomFieldUserSeeder::class);
        $this->seed(CustomColumnUserSeeder::class);
    }
}
