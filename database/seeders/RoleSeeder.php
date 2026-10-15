<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        Role::create([
            'name' => 'teacher',
            'display_name' => 'Teacher',
            'description' => 'Can create and manage classrooms, schedules, and sessions',
        ]);

        Role::create([
            'name' => 'student',
            'display_name' => 'Student',
            'description' => 'Can join classrooms and participate in sessions',
        ]);
    }
}
