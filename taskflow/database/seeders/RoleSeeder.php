<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            // System level
            ['name' => 'super-admin', 'guard_name' => 'api'],

            // Workspace level
            ['name' => 'owner',  'guard_name' => 'api'],
            ['name' => 'admin',  'guard_name' => 'api'],
            ['name' => 'member', 'guard_name' => 'api'],

            // Project level
            ['name' => 'project-manager', 'guard_name' => 'api'],
            ['name' => 'developer',       'guard_name' => 'api'],
        ];

        foreach ($roles as $role) {
            Role::firstOrCreate(
                ['name' => $role['name'], 'guard_name' => $role['guard_name']]
            );
        }

        $this->command->info('✅ Roles seeded successfully!');
    }
}
