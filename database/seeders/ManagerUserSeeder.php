<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ManagerUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
{
\App\Models\User::firstOrCreate(
['email' => 'manager@taskhub.test'],
[
'name' => 'Manager',
'password' => bcrypt('password'),
'role' => \App\Models\User::ROLE_MANAGER,
]
);
}
}
