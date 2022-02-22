<?php

namespace TaylorNetwork\UsernameGenerator\Tests\Models;

use TaylorNetwork\UsernameGenerator\Generator;

class CustomColumnUser extends TraitedUser
{
    protected $table = 'custom_column_users';

    // This is required if custom column is not set in the config file
    protected $usernameColumn = 'identifier';

    public function generatorConfig(Generator &$generator): void
    {
        $generator->setConfig([
            'model'  => $this,
            'column' => 'identifier',
        ]);
    }
}
