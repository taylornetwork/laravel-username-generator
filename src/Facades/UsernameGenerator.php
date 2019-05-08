<?php


namespace TaylorNetwork\UsernameGenerator\Facades;

use Illuminate\Support\Facades\Facade;


class UsernameGenerator extends Facade
{
    /**
     * @inheritDoc
     */
    protected static function getFacadeAccessor()
    {
        return 'UsernameGenerator';
    }

}