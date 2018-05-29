<?php

namespace TaylorNetwork\Tests;


use TaylorNetwork\UsernameGenerator\FindSimilarUsernames;

class TestModel
{
    use FindSimilarUsernames;

    public $attributes = [];

    public static function where(...$args)
    {
        return new static;
    }

    public function get()
    {
        return [$this->attributes];
    }
}