<?php

namespace TaylorNetwork\UsernameGenerator\Tests\Units;

use TaylorNetwork\UsernameGenerator\Tests\Models\CustomColumnUser;
use TaylorNetwork\UsernameGenerator\Tests\Models\CustomConfigUser;
use TaylorNetwork\UsernameGenerator\Tests\Models\TraitedUser;
use TaylorNetwork\UsernameGenerator\Tests\TestCase;

class TraitTest extends TestCase
{
    public function testEmptyEloquentSave()
    {
        $user = new TraitedUser([
            'name' => 'Eloquent Save',
        ]);
        $user->save();

        // Tests whether the username attribute was added
        $this->assertEquals('eloquentsave', $user->username);

        // Tests whether the username attribute got actually saved to the database
        $this->assertEquals('eloquentsave', TraitedUser::find($user->id)->username);
    }

    public function testEmptyEloquentCreate()
    {
        // Tests whether the username attribute was added
        $this->assertEquals('eloquentcreate', TraitedUser::create(['name' => 'Eloquent Create'])->username);

        // Tests whether the username attribute got actually saved to the database
        $this->assertEquals('eloquentcreate', TraitedUser::where('name', 'Eloquent Create')->first()->username);
    }

    public function testFilledValidation()
    {
        $user = new TraitedUser([
            'name'     => 'Filled Validation',
            'username' => 'UniqueUser!',
        ]);
        $user->save();

        // 'uniqueuser' username already exists in the db and should be made unique
        $this->assertEquals('uniqueuser1', $user->username);
        $this->assertEquals('uniqueuser1', TraitedUser::find($user->id)->username);
    }

    public function testCustomModelConfigFilled()
    {
        $user = new CustomConfigUser([
            'name'     => 'Testing User',
            'username' => 'usertesting',
        ]);
        $user->save();

        // Separator wouldn't show because the username filled has no spaces
        $this->assertEquals('USERTESTING', $user->username);
        $this->assertEquals('USERTESTING', CustomConfigUser::find($user->id)->username);
    }

    public function testCustomModelConfigEmpty()
    {
        $user = new CustomConfigUser([
            'name'     => 'Testing User',
        ]);
        $user->save();

        $this->assertEquals('TESTING+USER', $user->username);
        $this->assertEquals('TESTING+USER', CustomConfigUser::find($user->id)->username);
    }

    public function testCustomColumnUnique()
    {
        $user = new CustomColumnUser([
            'name' => 'Column User',
        ]);
        $user->save();

        // Username column is 'identifier' on this model
        $this->assertEquals('columnuser5', $user->identifier);
        $this->assertEquals('columnuser5', CustomColumnUser::find($user->id)->identifier);
    }
}
