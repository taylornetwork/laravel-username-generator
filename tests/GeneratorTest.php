<?php

require(__DIR__.'/../src/ServiceProvider.php');
require(__DIR__.'/../src/FindSimilarUsernames.php');
require(__DIR__.'/../src/Generator.php');
require(__DIR__.'/TestModel.php');
require(__DIR__.'/TestUser.php');
require(__DIR__.'/SomeUser.php');
require(__DIR__.'/CustomConfigUser.php');

use Orchestra\Testbench\TestCase;
use TaylorNetwork\UsernameGenerator\Generator;
use TaylorNetwork\Tests\TestUser;
use TaylorNetwork\Tests\SomeUser;
use TaylorNetwork\Tests\CustomConfigUser;
use TaylorNetwork\UsernameGenerator\ServiceProvider;

class GeneratorTest extends TestCase
{

    protected function getPackageProviders($app)
    {
        return [ ServiceProvider::class ];
    }

    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('username_generator.model', TestUser::class);
    }

    public function testOldMakeUsername()
    {
        $g = new Generator();
        $this->assertEquals('testuser1', $g->makeUsername('Test User'));
    }

    public function testDefaultConfig()
    {
        $g = new Generator();
        $this->assertEquals('testuser1', $g->generate('Test User'));
    }

    public function testNotUnique()
    {
        $g = new Generator([ 'unique' => false ]);
        $this->assertEquals('testuser', $g->generate('Test User'));
    }

    public function testMixedCaseNotUnique()
    {
        $g = new Generator([ 'case' => 'mixed', 'unique' => false ]);
        $this->assertEquals('TestUser', $g->generate('Test User'));
    }

    public function testUppercaseUniqueSeparator()
    {
        $g = new Generator([ 'case' => 'upper', 'separator' => '_' ]);
        $this->assertEquals('TEST_USER_1', $g->generate('Test User'));
    }

    public function testGenerateForModel()
    {
        $g = new Generator();
        $this->assertEquals('testuser1', $g->generateFor(new TestUser));
    }

    public function testTrait()
    {
        $model = new SomeUser();
        $model->generateUsername();
        $this->assertEquals('someuser1', $model->attributes['username']);
    }

    public function testTraitConfig()
    {
        $model = new CustomConfigUser();
        $model->generateUsername();
        $this->assertEquals('custom_config', $model->attributes['username']);
    }

    public function testTrimOtherChars()
    {
        $g = new Generator();
        $this->assertEquals('testuser1', $g->generate('Test, |User...'));
    }

    public function testBackwardsConstructWithName()
    {
        $g = new Generator('Test User');
        $this->assertEquals('testuser1', $g->generate());
    }
}