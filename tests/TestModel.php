<?php

namespace TaylorNetwork\Tests;

use TaylorNetwork\UsernameGenerator\FindSimilarUsernames;

class TestModel
{
    use FindSimilarUsernames;

    protected $attributes = [];

    public static function where(...$args)
    {
        return new static();
    }

    public function get()
    {
        return [$this->attributes];
    }

    public function getAttribute($attribute)
    {
        return $this->attributes[$attribute];
    }
}
