<?php

namespace TaylorNetwork\Tests\Environment;

use Illuminate\Database\Seeder;

class TestDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        BaseUser::create(['name' => 'Test User', 'username' => 'testuser']);
        BaseUser::create(['name' => 'Test User', 'username' => 'test_user']);
        BaseUser::create(['name' => 'Custom Config', 'username' => 'custom_config']);
        BaseUser::create(['name' => 'Multi Test', 'username' => 'multitest']);
        BaseUser::create(['name' => 'Multi Test', 'username' => 'multitest1']);

        BaseUser::create(['name' => 'T', 'username' => 't']);

        CustomColumnUser::create(['name' => 'Custom Column']);
        CustomColumnUser::create(['name' => 'Custom Column']);

        EmailUser::create(['email' => 'test_user@example.com', 'username' => 'testuser']);
        EmailUser::create(['email' => 'test.user@example.com', 'username' => 'testuser1']);

        CustomFieldUser::create(['fullName' => 'Test User', 'username' => 'testuser']);
    }
}
