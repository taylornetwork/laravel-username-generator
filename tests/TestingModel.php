<?php

namespace TaylorNetwork\Tests;

use TaylorNetwork\UsernameGenerator\FindSimilarUsernames;

class TestingModel
{
    use FindSimilarUsernames;

    public $column;
    public $operator;
    public $search;

    public static function where($column, $operator, $search = null)
    {
        if($search === null) {
            $search = $operator;
            $operator = '=';
        }

        $self = new self;
        $self->column = $column;
        $self->operator = $operator;
        $self->search = $search;
        return $self;
    }

    public function get()
    {
        return [
            [ 'name' => 'Test User', 'username' => 'testuser' ]
        ];
    }
}