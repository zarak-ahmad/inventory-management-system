<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        $allPermissionIds = Permission::query()->pluck('id');

        $matrix = [
            'Super Admin' => '*',
            'Admin' => '*',
            'Manager' => [
                'view_dashboard',
                'manage_categories',
                'manage_brands',
                'manage_units',
                'manage_suppliers',
                'manage_customers',
                'manage_products',
                'manage_sales',
                'manage_inventory',
                'manage_stock_adjustments',
                'view_reports',
            ],
            'Inventory Manager' => [
                'view_dashboard',
                'manage_categories',
                'manage_brands',
                'manage_units',
                'manage_suppliers',
                'manage_products',
                'manage_inventory',
                'manage_stock_adjustments',
                'view_reports',
            ],
            'Cashier' => [
                'view_dashboard',
                'manage_customers',
                'manage_sales',
            ],
        ];

        foreach ($matrix as $roleName => $permissions) {
            $role = Role::query()->where('name', $roleName)->firstOrFail();

            if ($permissions === '*') {
                $role->permissions()->sync($allPermissionIds);

                continue;
            }

            $permissionIds = Permission::query()
                ->whereIn('name', $permissions)
                ->pluck('id');

            $role->permissions()->sync($permissionIds);
        }
    }
}
