<?php

namespace TaylorNetwork\Tests\Environment;

class CustomConfigUser extends TraitedUser
{
    public function generatorConfig(&$generator): void
    {
        $generator->setConfig('separator', '_');
        $generator->setConfig('unique', false);
    }
}
