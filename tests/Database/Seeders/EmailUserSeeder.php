<?php

namespace TaylorNetwork\UsernameGenerator\Tests\Database\Seeders;

use Illuminate\Database\Seeder;
use TaylorNetwork\UsernameGenerator\Tests\Models\EmailUser;

class EmailUserSeeder extends Seeder
{
    public function run()
    {
        EmailUser::create(['email' => 'testemail@example.com', 'username' => 'testemail']);
    }
}
