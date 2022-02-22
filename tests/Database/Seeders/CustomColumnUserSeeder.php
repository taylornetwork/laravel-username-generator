<?php

namespace TaylorNetwork\UsernameGenerator\Tests\Database\Seeders;

use Illuminate\Database\Seeder;
use TaylorNetwork\UsernameGenerator\Tests\Models\CustomColumnUser;

class CustomColumnUserSeeder extends Seeder
{
    public function run()
    {
        CustomColumnUser::create(['name' => 'Column User1', 'identifier' => 'columnuser0']);
        CustomColumnUser::create(['name' => 'Column User2', 'identifier' => 'columnuser1']);
        CustomColumnUser::create(['name' => 'Column User3', 'identifier' => 'columnuser2']);
        CustomColumnUser::create(['name' => 'Column User4', 'identifier' => 'columnuser3']);
        CustomColumnUser::create(['name' => 'Column User5', 'identifier' => 'columnuser4']);
    }
}
