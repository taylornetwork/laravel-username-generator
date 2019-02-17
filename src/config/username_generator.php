<?php

return [

    'unique' => true,

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

    'model' => '\\App\\User',

    'column' => 'username',

];
