<?php

namespace TaylorNetwork\Tests\Environment;

use Illuminate\Database\Eloquent\Model;
use TaylorNetwork\UsernameGenerator\FindSimilarUsernames;

class BaseUser extends Model
{
    use FindSimilarUsernames;

    protected $guarded = [];

    protected $table = 'users';
}
