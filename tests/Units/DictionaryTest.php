<?php

namespace TaylorNetwork\UsernameGenerator\Tests\Units;

use TaylorNetwork\UsernameGenerator\Generator;
use TaylorNetwork\UsernameGenerator\Tests\TestCase;

class DictionaryTest extends TestCase
{
    public function testDictionary()
    {
        $g = new Generator();

        $this->assertIsString($g->generate());
    }

    public function testCustomDictionary()
    {
        $g = new Generator([
            'dictionary' => [
                'nouns'      => ['test'],
                'adjectives' => ['simple'],
            ],
        ]);

        $this->assertEquals('simpletest', $g->generate());
    }
}
