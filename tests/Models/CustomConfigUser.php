<?php

namespace TaylorNetwork\UsernameGenerator\Tests\Models;

use TaylorNetwork\UsernameGenerator\Generator;

class CustomConfigUser extends TraitedUser
{
    public function generatorConfig(Generator &$generator): void
    {
        $generator->setConfig([
            'separator'  => '+',
            'case'       => 'upper',
        ]);
    }
}
