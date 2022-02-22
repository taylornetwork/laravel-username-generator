<?php

namespace TaylorNetwork\UsernameGenerator\Tests\Database\Seeders;

use Illuminate\Database\Seeder;
use TaylorNetwork\UsernameGenerator\Tests\Models\CustomFieldUser;

class CustomFieldUserSeeder extends Seeder
{
    public function run()
    {
        CustomFieldUser::create(['full_name' => 'Test User', 'username' => 'testuser']);
        CustomFieldUser::create(['full_name' => 'Test User', 'username' => 'testuser1']);
    }
}
