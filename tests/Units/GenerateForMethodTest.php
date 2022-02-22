<?php

namespace TaylorNetwork\UsernameGenerator\Tests\Units;

use TaylorNetwork\UsernameGenerator\Facades\UsernameGenerator;
use TaylorNetwork\UsernameGenerator\Generator;
use TaylorNetwork\UsernameGenerator\Tests\Models\CustomFieldUser;
use TaylorNetwork\UsernameGenerator\Tests\Models\EmailUser;
use TaylorNetwork\UsernameGenerator\Tests\Models\User;
use TaylorNetwork\UsernameGenerator\Tests\TestCase;

class GenerateForMethodTest extends TestCase
{
    public function testGenerateForMethod()
    {
        $model = new User([
            'name' => 'Test User',
        ]);

        $g = new Generator();

        $this->assertEquals('testuser', $g->generateFor($model));
    }

    public function testImplicitDriverEmail()
    {
        $model = new EmailUser([
            'email' => 'testuser@example.com',
        ]);

        $this->assertEquals('testuser', UsernameGenerator::generateFor($model));
    }

    public function testExplicitDriverEmail()
    {
        $model = new EmailUser([
            'email' => 'testuser@example.com',
        ]);

        $g = new Generator();
        $g->setDriver('email');

        $this->assertEquals('testuser', $g->generateFor($model));
    }

    public function testFieldMapString()
    {
        $model = new CustomFieldUser([
            'full_name' => 'Test User',
        ]);

        $g = new Generator([
            'model'     => CustomFieldUser::class,
            'field_map' => [
                'name' => 'full_name',
            ],
        ]);

        $this->assertEquals('testuser2', $g->generateFor($model));
    }

    public function testFieldMapArray()
    {
        $model = new CustomFieldUser([
            'full_name' => 'Test User',
        ]);

        $g = new Generator([
            'model'     => CustomFieldUser::class,
            'field_map' => [
                'name' => ['full_name', 'fullName'],
            ],
        ]);

        $this->assertEquals('testuser2', $g->generateFor($model));
    }

    public function testUnusedFieldMap()
    {
        $model = new User([
            'name' => 'Test User',
        ]);

        $g = new Generator([
            'field_map' => [
                'name' => ['full_name', 'fullName'],
            ],
        ]);

        $this->assertEquals('testuser', $g->generateFor($model));
    }
}
