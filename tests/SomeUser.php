<?php

namespace TaylorNetwork\Tests;

use TaylorNetwork\UsernameGenerator\GeneratesUsernames;

class SomeUser extends TestModel
{
    use GeneratesUsernames;

    public $attributes = ['name' => 'Some User', 'username' => null];
}
