<?php

namespace TaylorNetwork\UsernameGenerator\Tests\Database\Seeders;

use Illuminate\Database\Seeder;
use TaylorNetwork\UsernameGenerator\Tests\Models\User;

class UserSeeder extends Seeder
{
    public function run()
    {
        $this->seedShorts();
        $this->seedUniqueUser();
        $this->seedMultiUser();
    }

    protected function seedShorts()
    {
        User::create(['name' => 'Test User', 'username' => 'tee']);
        User::create(['name' => 'Test User', 'username' => 'te']);
        User::create(['name' => 'Test User', 'username' => 't']);
    }

    protected function seedUniqueUser()
    {
        User::create(['name' => 'Unique User', 'username' => 'uniqueuser']);
        User::create(['name' => 'Unique User', 'username' => 'unique_user']);
    }

    protected function seedMultiUser()
    {
        User::create(['name' => 'Multi User', 'username' => 'multi_user']);
        User::create(['name' => 'Multi User', 'username' => 'multi_user_1']);
        User::create(['name' => 'Multi User', 'username' => 'multi_user_2']);
        User::create(['name' => 'Multi User', 'username' => 'multi_user_3']);
        User::create(['name' => 'Multi User', 'username' => 'multi_user_4']);
        User::create(['name' => 'Multi User', 'username' => 'multi_user_5']);
        User::create(['name' => 'Multi User', 'username' => 'multi_user_6']);
        User::create(['name' => 'Multi User', 'username' => 'multi_user_7']);
        User::create(['name' => 'Multi User', 'username' => 'multi_user_8']);
        User::create(['name' => 'Multi User', 'username' => 'multi_user_9']);
        User::create(['name' => 'Multi User', 'username' => 'multi_user_10']);
        User::create(['name' => 'Multi User', 'username' => 'multi_user_11']);
    }
}
