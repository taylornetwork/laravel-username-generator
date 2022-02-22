<?php

namespace TaylorNetwork\UsernameGenerator\Tests\Models;

use Illuminate\Database\Eloquent\Model;
use TaylorNetwork\UsernameGenerator\FindSimilarUsernames;

class User extends Model
{
    use FindSimilarUsernames;

    protected $guarded = [];

    protected $table = 'users';
}
