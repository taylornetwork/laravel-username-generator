<?php

namespace TaylorNetwork\UsernameGenerator\Tests\Units;

use TaylorNetwork\UsernameGenerator\Generator;
use TaylorNetwork\UsernameGenerator\Tests\TestCase;

class UniqueTest extends TestCase
{
    public function testUnique()
    {
        $g = new Generator();

        $this->assertEquals('uniqueuser1', $g->generate('Unique User'));
    }

    public function testUniqueSeparator()
    {
        $g = new Generator([
            'separator' => '_',
        ]);

        $this->assertEquals('unique_user_1', $g->generate('Unique User'));
    }

    public function testMultiUniqueWithSeparator()
    {
        $g = new Generator([
            'separator' => '_',
        ]);

        $this->assertEquals('multi_user_12', $g->generate('Multi User'));
    }

    public function testUniqueUsingMax()
    {
        $g = new Generator();
        $this->assertEquals('randomnum23', $g->generate('Random Num'));
    }
}
