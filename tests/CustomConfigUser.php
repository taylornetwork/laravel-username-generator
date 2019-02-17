<?php

namespace TaylorNetwork\Tests;

use TaylorNetwork\UsernameGenerator\GeneratesUsernames;

class CustomConfigUser extends TestModel
{
    use GeneratesUsernames;

    public $attributes = ['name' => 'Custom Config', 'username' => null];

    public function generatorConfig(&$generator)
    {
        $generator->setConfig(['unique'=> false, 'separator' => '_']);
    }
}
