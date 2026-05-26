<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Create basic roles with codes.
     */
    public function run(): void
    {
        Role::firstOrCreate(
            ['code' => 'admin'],
            [
                'name' => 'Administrator',
                'description' => 'Full access to the system'
            ]
        );

        Role::firstOrCreate(
            ['code' => 'manager'],
            [
                'name' => 'Manager',
                'description' => 'Working with clients, deals and tasks'
            ]
        );

        Role::firstOrCreate(
            ['code' => 'employee'],
            [
                'name' => 'Employee',
                'description' => 'Executing tasks and working with assigned objects'
            ]
        );

    }

}
