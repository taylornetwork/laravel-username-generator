<?php

namespace TaylorNetwork\UsernameGenerator\Tests\Units;

use TaylorNetwork\UsernameGenerator\Drivers\NameDriver;
use TaylorNetwork\UsernameGenerator\Generator;
use TaylorNetwork\UsernameGenerator\Tests\TestCase;

class DriverTest extends TestCase
{
    public function testSetDriverKey()
    {
        $g = new Generator();
        $g->setDriver('name');
        $this->assertEquals('testuser', $g->generate('test user'));
    }

    public function testSetDriverClass()
    {
        $g = new Generator();
        $g->setDriver(NameDriver::class);
        $this->assertEquals('testuser', $g->generate('test user'));
    }

    public function testEmailDriver()
    {
        $g = new Generator();
        $g->setDriver('email');
        $this->assertEquals('test', $g->generate('test@example.com'));
    }

    public function testCaller()
    {
        $g = new Generator();
        $this->assertEquals('test', $g->usingEmail()->generate('test@example.com'));
    }

    public function testStaticCaller()
    {
        $this->assertEquals('test', Generator::usingEmail()->generate('test@example.com'));
    }
}
