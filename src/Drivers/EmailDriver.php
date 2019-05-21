<?php

namespace TaylorNetwork\UsernameGenerator\Drivers;

class EmailDriver extends BaseDriver
{
    public $field = 'email';

    public function preHook(string $text): string
    {
        return preg_replace('/(@.*)$/', '', $text);
    }
}
