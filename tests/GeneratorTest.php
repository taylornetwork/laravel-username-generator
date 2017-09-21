<?php

require(__DIR__.'/../src/ServiceProvider.php');
require(__DIR__.'/../src/FindSimilarUsernames.php');
require(__DIR__.'/../src/Generator.php');
require(__DIR__.'/TestingModel.php');

use Orchestra\Testbench\TestCase;
use TaylorNetwork\UsernameGenerator\Generator;
use TaylorNetwork\Tests\TestingModel;
use TaylorNetwork\UsernameGenerator\ServiceProvider;

class GeneratorTest extends TestCase
{

    protected function getPackageProviders($app)
    {
        return [ ServiceProvider::class ];
    }

    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('username_generator.class', TestingModel::class);
    }

    public function testUnique()
    {
        $generator = new Generator();
        $username = $generator->makeUsername('Test User');

        $this->assertEquals('testuser1', $username);
    }

    public function testNotUnique()
    {
        $generator = new Generator();
        $generator->setConfig([
            'unique' => false,
        ]);

        $this->assertEquals('testuser', $generator->makeUsername('Test User'));
    }

    public function testUniqueWithSeparator()
    {
        $generator = new Generator();
        $generator->setConfig([
            'separator' => '_'
        ]);

        $this->assertEquals('test_user_1', $generator->makeUsername('Test User'));
    }

    public function testNotUniqueMixedCase()
    {
        $generator = new Generator();
        $generator->setConfig([
            'unique' => false,
            'case' => 'mixed'
        ]);

        $this->assertEquals('TestUser', $generator->makeUsername('Test User'));
    }

}