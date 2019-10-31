<?php

namespace TaylorNetwork\Tests;

use Gen;
use Orchestra\Testbench\TestCase;
use TaylorNetwork\UsernameGenerator\Facades\UsernameGenerator;
use TaylorNetwork\UsernameGenerator\Generator;
use TaylorNetwork\UsernameGenerator\ServiceProvider;

class GeneratorTest extends TestCase
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
        $app['config']->set('username_generator.model', TestUser::class);
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

    public function testFacade()
    {
        $this->assertEquals('testuser1', UsernameGenerator::generate('testuser'));
    }

    public function testAliasFacade()
    {
        $this->assertEquals('testuser1', Gen::generate('testuser'));
    }

    public function testEmail()
    {
        $this->assertEquals('testuser1', UsernameGenerator::usingEmail()->generate('test.user@example.com'));
    }

    public function testSetDriver()
    {
        $generator = new Generator();
        $generator->setDriver('email');
        $this->assertEquals('testuser1', $generator->generate('test_user777@example.com'));
    }

    public function testAllowExtraChars()
    {
        $generator = new Generator(['allowed_characters' => 'a-zA-Z0-9_\- ', 'unique' => false]);
        $this->assertEquals('use-r_test777', $generator->usingEmail()->generate('use-r_test777@example.com'));
    }

    public function testMinLength()
    {
        $username = UsernameGenerator::setConfig('min_length', 6)->generate('Te St');
        $this->assertTrue((bool) preg_match('/test\d\d/', $username));
        $this->assertEquals(6, strlen($username));
    }

    public function testIgnoreMinLength()
    {
        $this->assertEquals('t', UsernameGenerator::setConfig('unique', false)->generate('T'));
    }

    public function testCustomColumn()
    {
        $model = new CustomColumn();
        $model->generateUsername();
        $this->assertEquals('custom*column*1', $model->attributes['identifier']);
    }

    public function testRandom()
    {
        $this->assertIsString(UsernameGenerator::generate());
    }
}
