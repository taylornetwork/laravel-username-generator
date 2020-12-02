<?php

namespace TaylorNetwork\UsernameGenerator\Facades;

use Illuminate\Support\Facades\Facade;

class UsernameGenerator extends Facade
{
    /**
     * {@inheritdoc}
     */
    protected static function getFacadeAccessor(): string
    {
        return 'UsernameGenerator';
    }
}
