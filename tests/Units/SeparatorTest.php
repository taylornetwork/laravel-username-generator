<?php

namespace TaylorNetwork\UsernameGenerator\Tests\Units;

use TaylorNetwork\UsernameGenerator\Generator;
use TaylorNetwork\UsernameGenerator\Tests\TestCase;

class SeparatorTest extends TestCase
{
    public function testNoSeparator()
    {
        $g = new Generator([
            'separator' => '',
        ]);

        $this->assertEquals('separatortest', $g->generate('separator test'));
    }

    public function testUnderscore()
    {
        $g = new Generator([
            'separator' => '_',
        ]);

        $this->assertEquals('separator_test', $g->generate('separator test'));
    }

    public function testDash()
    {
        $g = new Generator([
            'separator' => '-',
        ]);

        $this->assertEquals('separator-test', $g->generate('separator test'));
    }

    public function testMultiCharacter()
    {
        $g = new Generator([
            'separator' => '*&',
        ]);

        $this->assertEquals('separator*&test', $g->generate('separator test'));
    }
}
