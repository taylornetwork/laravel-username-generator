<?php

namespace TaylorNetwork\UsernameGenerator\Tests\Units;

use TaylorNetwork\UsernameGenerator\Facades\UsernameGenerator;
use TaylorNetwork\UsernameGenerator\Tests\TestCase;

class AliasTest extends TestCase
{
    public function testAlias()
    {
        $this->assertEquals('uniqueuser1', \Gen::generate('unique user'));
    }

    public function testFacade()
    {
        $this->assertEquals('uniqueuser1', UsernameGenerator::generate('unique user'));
    }
}
