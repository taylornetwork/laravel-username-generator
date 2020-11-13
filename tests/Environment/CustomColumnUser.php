<?php

namespace TaylorNetwork\Tests\Environment;

class CustomColumnUser extends TraitedUser
{
    protected $table = 'custom_column';

    protected $usernameColumn = 'identifier';

    public function generatorConfig(&$generator): void
    {
        $generator->setConfig(['unique' => true, 'separator' => '*', 'column' => 'identifier', 'model' => $this]);
    }
}
