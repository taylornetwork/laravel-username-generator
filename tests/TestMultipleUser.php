<?php 

namespace TaylorNetwork\Tests;

use TaylorNetwork\UsernameGenerator\GeneratesUsernames;

class TestMultipleUser extends TestModel
{
	use GeneratesUsernames;

    public $attributes = [ 'name' => 'Test User', 'username' => null ];

	public function get()
    {
        return [
        	[
        		'name' => 'Test User',
        		'username' => 'testuser',	
        	],

        	[
        		'name' => 'Test User',
        		'username' => 'testuser1',
        	],
        ];
    }

    public function generatorConfig(&$generator)
    {
        $generator->setConfig('model', $this);
    }
}