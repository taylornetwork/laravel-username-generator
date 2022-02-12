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
use TaylorNetwork\UsernameGenerator\Support\Exceptions\GeneratorException;
use TaylorNetwork\UsernameGenerator\Support\Exceptions\UsernameTooLongException;

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
        $this->assertEquals('TEST_USER', $g->generate('Test User'));
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
            'model'     => DefaultUser::class,
            'field_map' => [
                'name' => 'fullName',
            ],
        ]);

        $username = $g->generateFor(new DefaultUser(['name' => 'Test User']));
        $this->assertEquals('testuser1', $username);
    }

    public function testUsernameTooLong()
    {
        $g = new Generator([
            'max_length' => 8,
        ]);

        $this->assertEquals('testuse', $g->generate('Test User'));
    }

    public function testUsernameTooLongException()
    {
        $g = new Generator([
            'max_length'                  => 6,
            'throw_exception_on_too_long' => true,
        ]);

        $this->expectException(UsernameTooLongException::class);
        $g->generate('Test User');
    }

    public function testUsernameFailure()
    {
        $g = new Generator([
            'max_length' => 1,
        ]);

        $this->expectException(GeneratorException::class);
        $g->generate('Test User');
    }

    public function testLowerCyrillicString()
    {
        $g = new Generator([
            'unique'             => false,
            'allowed_characters' => 'А-Яа-яA-Za-z',
            'case'               => 'lower',
            'convert_to_ascii'   => false,
        ]);

        $this->assertEquals('роман', $g->generate('Роман'));
    }

    public function testUpperCyrillicString()
    {
        $g = new Generator([
            'unique'             => false,
            'allowed_characters' => 'А-Яа-яA-Za-z',
            'case'               => 'upper',
            'convert_to_ascii'   => false,
        ]);

        $this->assertEquals('РОМАН', $g->generate('Роман'));
    }

    public function testCyrillcMixed()
    {
        $g = new Generator([
            'unique'             => false,
            'allowed_characters' => 'А-Яа-яA-Za-z',
            'case'               => 'mixed',
            'convert_to_ascii'   => false,
        ]);

        $this->assertEquals('РоманTest', $g->generate('Роман Test 1'));
    }

    public function testCyrillicToAsciiMixed()
    {
        $g = new Generator([
            'unique' => false,
            'case'   => 'mixed',
        ]);

        $this->assertEquals('Roman', $g->generate('Роман'));
    }

    public function testCyrillicToAsciiLower()
    {
        $g = new Generator([
            'unique' => false,
            'case'   => 'lower',
        ]);

        $this->assertEquals('roman', $g->generate('Роман'));
    }

    public function testTitleCase()
    {
        $g = new Generator([
            'case' => 'title',
        ]);

        $this->assertEquals('TestUser', $g->generate('test user'));
    }

    public function testUcfirstCase()
    {
        $g = new Generator([
            'case' => 'ucfirst',
        ]);

        $this->assertEquals('Testuser', $g->generate('test user'));
    }

    public function testGreek()
    {
        $g = new Generator([
            'unique'             => false,
            'case'               => 'mixed',
            'convert_to_ascii'   => false,
            'allowed_characters' => '\p{Greek}A-Za-z ',
        ]);
        $this->assertEquals('Σὲγνωρίζωἀπὸτὴνκόψη', $g->generate('Σὲ γνωρίζω ἀπὸ τὴν κόψη'));
    }

    public function testGreekToLowerWithSeparator()
    {
        $g = new Generator([
            'unique'             => false,
            'case'               => 'lower',
            'convert_to_ascii'   => false,
            'allowed_characters' => '\p{Greek}A-Za-z ',
            'separator'          => '-',
        ]);
        $this->assertEquals('σὲ-γνωρίζω-ἀπὸ-τὴν-κόψη', $g->generate('Σὲ γνωρίζω ἀπὸ τὴν κόψη'));
    }

    public function testCyrillicProperty()
    {
        $g = new Generator([
            'unique'             => false,
            'case'               => 'upper',
            'convert_to_ascii'   => false,
            'allowed_characters' => '\p{Cyrillic}\p{Latin}\s ',
            'separator'          => '_',
        ]);

        $this->assertEquals('ЗАРЕГИСТРИРУЙТЕСЬ_СЕЙЧАС_НА_ДЕСЯТУЮ_МЕЖДУНАРОДНУЮ_КОНФЕРЕНЦИЮ_ПО', $g->generate('Зарегистрируйтесь сейчас на Десятую Международную Конференцию по'));
    }

    public function testCustomDictionary()
    {
        $g = new Generator([
            'unique'     => false,
            'case'       => 'title',
            'dictionary' => [
                'adjectives' => ['simple'],
                'nouns'      => ['test'],
            ],
        ]);

        $this->assertEquals('SimpleTest', $g->generate());
    }

    public function testMulti()
    {
        $count = 20;

        while ($count > 0) {
            $model = new TraitedUser(['name' => 'Multiple Names']);
            $model->save();
            $count--;
        }

        $this->assertEquals(20, TraitedUser::where('name', 'Multiple Names')->count());
        $this->assertNotNull(TraitedUser::where('username', 'multiplenames19')->first());
    }
}
