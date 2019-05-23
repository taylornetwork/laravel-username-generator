<?php

use App\User;
use TaylorNetwork\UsernameGenerator\Drivers\EmailDriver;
use TaylorNetwork\UsernameGenerator\Drivers\NameDriver;

return [

    'unique' => true,

    'min_length' => 0,

    'throw_exception_on_too_short' => false,

    /*
     |----------------------------------------------------------------------------
     | Username Character Case
     |----------------------------------------------------------------------------
     |
     | Options are:
     |      - lower
     |          For all lowercase characters (ie: johnsmith)
     |
     |      - upper
     |          For all uppercase characters (ie: JOHNSMITH)
     |
     |      - mixed
     |          Allow mixed upper and lower cases for characters (ie: JohnSmith)
     |
     */
    'case' => 'lower',

    'separator' => '',

    'model' => User::class,

    'column' => 'username',

    'allowed_characters' => 'a-zA-Z ',

    'drivers' => [
        'name'  => NameDriver::class,
        'email' => EmailDriver::class,
    ],
];
