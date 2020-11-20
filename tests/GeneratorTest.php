<?php

namespace TaylorNetwork\Tests;

use Gen;
use Orchestra\Testbench\TestCase;
use TaylorNetwork\Tests\Environment\CustomColumnUser;
use TaylorNetwork\Tests\Environment\CustomConfigUser;
use TaylorNetwork\Tests\Environment\CustomFieldUser;
use TaylorNetwork\Tests\Environment\DefaultUser;
use TaylorNetwork\Tests\Environment\EmailUser;
use TaylorNetwork\Tests\Environment\TestDatabaseSeeder;
use TaylorNetwork\Tests\Environment\TraitedUser;
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
        $app['config']->set('username_generator.model', DefaultUser::class);

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
        $this->loadMigrationsFrom(implode(DIRECTORY_SEPARATOR, [__DIR__, 'Environment', 'migrations']));
        $this->seed(TestDatabaseSeeder::class);
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
        $this->assertEquals('testuser1', $g->generateFor(new DefaultUser(['name' => 'Test User'])));
    }

    public function testTrait()
    {
        $model = new TraitedUser(['name' => 'Test User']);
        $model->generateUsername();
        $this->assertEquals('testuser1', $model->getAttribute('username'));
    }

    public function testTraitConfig()
    {
        $model = new CustomConfigUser(['name' => 'Custom Config']);
        $model->generateUsername();
        $this->assertEquals('custom_config', $model->getAttribute('username'));
    }

    public function testTrimOtherChars()
    {
        $g = new Generator();
        $this->assertEquals('testuser1', $g->generate('Test, |User...'));
    }

    public function testUniqueMultiple()
    {
        $model = new TraitedUser(['name' => 'Multi Test']);
        $model->generateUsername();
        $this->assertEquals('multitest2', $model->getAttribute('username'));
    }

    public function testTrimCharsWithSeparator()
    {
        $g = new Generator(['separator' => '-']);
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
        $generator = new Generator(['allowed_characters' => 'a-zA-Z0-9_\- ']);
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

    public function testCustomColumnMultiple()
    {
        $model = new CustomColumnUser(['name' => 'Custom Column']);
        $model->generateUsername();
        $this->assertEquals('custom*column*2', $model->getAttribute('identifier'));
    }

    public function testRandom()
    {
        $this->assertIsString(UsernameGenerator::generate());
    }

    public function testModelEmptyName()
    {
        $model = new TraitedUser();
        $model->generateUsername();
        $this->assertIsString($model->getAttribute('username'));
    }

    public function testGenerateForUsingEmail()
    {
        $g = new Generator(['model' => EmailUser::class]);
        $username = $g->generateFor(new EmailUser(['email' => 'testuser@exmaple.com']));
        $this->assertEquals('testuser2', $username);
    }

    public function testGenerateForUsingSetDriver()
    {
        $g = new Generator(['model' => EmailUser::class]);
        $g->setDriver('email');
        $username = $g->generateFor(new EmailUser(['email' => 'testuser@exmaple.com']));
        $this->assertEquals('testuser2', $username);
    }

    public function testGenerateForWithFieldMapString()
    {
        $g = new Generator([
            'model'     => CustomFieldUser::class,
            'field_map' => [
                'name' => 'fullName',
            ],
        ]);

        $username = $g->generateFor(new CustomFieldUser(['fullName' => 'Test User']));
        $this->assertEquals('testuser1', $username);
    }

    public function testGenerateForWithFieldMapArray()
    {
        $g = new Generator([
            'model'     => CustomFieldUser::class,
            'field_map' => [
                'name' => ['fullName'],
            ],
        ]);

        $username = $g->generateFor(new CustomFieldUser(['fullName' => 'Test User']));
        $this->assertEquals('testuser1', $username);
    }

    public function testFieldMapExistsButNotUsed()
    {
        $g = new Generator([
            'model' => DefaultUser::class,
            'field_map' => [
                'name' => 'fullName'
            ]
        ]);

        $username = $g->generateFor(new DefaultUser(['name' => 'Test User']));
        $this->assertEquals('testuser1', $username);
    }
}

