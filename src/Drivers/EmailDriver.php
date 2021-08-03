<?php

namespace TaylorNetwork\UsernameGenerator\Drivers;

class EmailDriver extends BaseDriver
{
    public $field = 'email';

    /**
     * Strip everything after the @ symbol.
     *
     * @param string $text
     *
     * @return string
     */
    public function first(string $text): string
    {
        return preg_replace('/(@.*)$/', '', $text);
    }
}
