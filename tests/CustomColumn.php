<?php

namespace TaylorNetwork\Tests;

use TaylorNetwork\UsernameGenerator\GeneratesUsernames;

class CustomColumn extends TestModel
{
    use GeneratesUsernames;

    public $attributes = ['name' => 'Custom Column', 'identifier' => null];

    public function generatorConfig(&$generator)
    {
        $generator->setConfig(['unique' => true, 'separator' => '*', 'column' => 'identifier']);
    }
}
