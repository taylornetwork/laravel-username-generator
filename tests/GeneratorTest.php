<?php

$loader = require __DIR__.'/../vendor/autoload.php';
$loader->addPsr4('TaylorNetwork\\Tests\\', __DIR__.'/');

if (!function_exists('config')) {
    function config($key, $default = null)
    {
        $config = include_once __DIR__.'/../src/config/username_generator.php';

        if (array_key_exists($key, $config)) {
            return $config[$key];
        }

        return $default;
    }
}

use Orchestra\Testbench\TestCase;
use TaylorNetwork\Tests\CustomConfigUser;
use TaylorNetwork\Tests\SomeUser;
use TaylorNetwork\Tests\TestMultipleUser;
use TaylorNetwork\Tests\TestUser;
use TaylorNetwork\UsernameGenerator\Generator;
use TaylorNetwork\UsernameGenerator\ServiceProvider;

class GeneratorTest extends TestCase
{
    protected function getPackageProviders($app)
    {
        return [ServiceProvider::class];
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
        $g = new Generator(['unique' => false]);
        $this->assertEquals('testuser', $g->generate('Test User'));
    }

    public function testMixedCaseNotUnique()
    {
        $g = new Generator(['case' => 'mixed', 'unique' => false]);
        $this->assertEquals('TestUser', $g->generate('Test User'));
    }

    public function testUppercaseUniqueSeparator()
    {
        $g = new Generator(['case' => 'upper', 'separator' => '_']);
        $this->assertEquals('TEST_USER_1', $g->generate('Test User'));
    }

    public function testGenerateForModel()
    {
        $g = new Generator();
        $this->assertEquals('testuser1', $g->generateFor(new TestUser()));
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

    public function testUniqueMultiple()
    {
        $model = new TestMultipleUser();
        $model->generateUsername();
        $this->assertEquals('testuser2', $model->attributes['username']);
    }

    public function testTrimCharsWithSeparator()
    {
        $g = new Generator(['separator' => '-', 'unique' => false]);
        $this->assertEquals('this-is-a-test-user', $g->generate('1THIS iS 1^^*A *T(E)s$t USER!***(((   '));
    }
}
