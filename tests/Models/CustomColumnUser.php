<?php

namespace TaylorNetwork\UsernameGenerator\Tests\Models;

use TaylorNetwork\UsernameGenerator\Generator;

class CustomColumnUser extends TraitedUser
{
    protected $table = 'custom_column_users';

    public function generatorConfig(Generator &$generator): void
    {
        $generator->setConfig([
            'model'  => $this,
            'column' => 'identifier',
        ]);
    }
}
