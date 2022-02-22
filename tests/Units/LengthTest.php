<?php

namespace TaylorNetwork\UsernameGenerator\Tests\Units;

use TaylorNetwork\UsernameGenerator\Generator;
use TaylorNetwork\UsernameGenerator\Support\Exceptions\GeneratorException;
use TaylorNetwork\UsernameGenerator\Support\Exceptions\UsernameTooLongException;
use TaylorNetwork\UsernameGenerator\Support\Exceptions\UsernameTooShortException;
use TaylorNetwork\UsernameGenerator\Tests\TestCase;

class LengthTest extends TestCase
{
    public function testTooShortPad()
    {
        $g = new Generator([
            'min_length' => 6,
        ]);
        $username = $g->generate('TEST');

        $this->assertMatchesRegularExpression('/test\d\d/', $username);
        $this->assertEquals(6, strlen($username));
    }

    public function testTooShortException()
    {
        $g = new Generator([
            'min_length'                   => 6,
            'throw_exception_on_too_short' => true,
        ]);

        $this->expectException(UsernameTooShortException::class);
        $g->generate('test');
    }

    public function testTooLongShrink()
    {
        $g = new Generator([
            'max_length' => 3,
        ]);

        $this->assertEquals('tes', $g->generate('test user'));
    }

    public function testTooLongException()
    {
        $g = new Generator([
            'max_length'                  => 3,
            'throw_exception_on_too_long' => true,
        ]);

        $this->expectException(UsernameTooLongException::class);
        $g->generate('test user');
    }

    public function testTooLongShrinkFailure()
    {
        $g = new Generator([
            'max_length' => 1,
        ]);

        // Username 't' already exists in db
        $this->expectException(GeneratorException::class);
        $g->generate('test user');
    }

    public function testTooLongShrinkUnique()
    {
        $g = new Generator([
            'max_length' => 3,
        ]);

        $this->assertEquals('te2', $g->generate('teeeeee'));
    }
}
