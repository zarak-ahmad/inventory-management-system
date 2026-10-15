<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            [
                'name' => 'Super Admin',
                'description' => 'Full system access including user management.',
            ],
            [
                'name' => 'Admin',
                'description' => 'Full operational access including users and settings.',
            ],
            [
                'name' => 'Manager',
                'description' => 'Manage inventory and reports.',
            ],
            [
                'name' => 'Inventory Manager',
                'description' => 'Manage products, suppliers, and stock.',
            ],
            [
                'name' => 'Cashier',
                'description' => 'View dashboard and manage customers.',
            ],
        ];

        foreach ($roles as $role) {
            Role::query()->updateOrCreate(
                ['name' => $role['name']],
                ['description' => $role['description']]
            );
        }
    }
}
