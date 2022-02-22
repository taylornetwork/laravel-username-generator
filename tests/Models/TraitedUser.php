<?php

namespace TaylorNetwork\UsernameGenerator\Tests\Models;

use TaylorNetwork\UsernameGenerator\GeneratesUsernames;

class TraitedUser extends User
{
    use GeneratesUsernames;
}
