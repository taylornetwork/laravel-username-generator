<?php

namespace TaylorNetwork\UsernameGenerator\Tests\Units;

use TaylorNetwork\UsernameGenerator\Generator;
use TaylorNetwork\UsernameGenerator\Tests\TestCase;

class CaseTest extends TestCase
{
    public function testLower()
    {
        $g = new Generator([
            'case' => 'lower',
        ]);

        $this->assertEquals('testuser', $g->generate('TeSt UsEr'));
    }

    public function testUpper()
    {
        $g = new Generator([
            'case' => 'upper',
        ]);

        $this->assertEquals('TESTUSER', $g->generate('TeSt UsEr'));
    }

    public function testTitle()
    {
        $g = new Generator([
            'case' => 'title',
        ]);

        $this->assertEquals('TestUser', $g->generate('TeSt UsEr'));
    }

    public function testMixed()
    {
        $g = new Generator([
            'case' => 'mixed',
        ]);

        $this->assertEquals('TeStUsEr', $g->generate('TeSt UsEr'));
    }

    public function testUcfirst()
    {
        $g = new Generator([
            'case' => 'ucfirst',
        ]);

        $this->assertEquals('Testuser', $g->generate('test user'));
    }
}
