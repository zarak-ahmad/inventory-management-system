<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            ['name' => 'view_dashboard', 'label' => 'View Dashboard', 'module' => 'Dashboard'],
            ['name' => 'manage_users', 'label' => 'Manage Users', 'module' => 'Users'],
            ['name' => 'manage_categories', 'label' => 'Manage Categories', 'module' => 'Categories'],
            ['name' => 'manage_brands', 'label' => 'Manage Brands', 'module' => 'Brands'],
            ['name' => 'manage_units', 'label' => 'Manage Units', 'module' => 'Units'],
            ['name' => 'manage_suppliers', 'label' => 'Manage Suppliers', 'module' => 'Suppliers'],
            ['name' => 'manage_customers', 'label' => 'Manage Customers', 'module' => 'Customers'],
            ['name' => 'manage_products', 'label' => 'Manage Products', 'module' => 'Products'],
            ['name' => 'manage_sales', 'label' => 'Manage Sales / POS', 'module' => 'Sales'],
            ['name' => 'manage_inventory', 'label' => 'Manage Inventory', 'module' => 'Inventory'],
            ['name' => 'manage_stock_adjustments', 'label' => 'Manage Stock Adjustments', 'module' => 'Inventory'],
            ['name' => 'view_reports', 'label' => 'View Reports', 'module' => 'Reports'],
            ['name' => 'manage_settings', 'label' => 'Manage Settings', 'module' => 'Settings'],
            ['name' => 'view_audit_logs', 'label' => 'View Audit Logs', 'module' => 'Audit'],
            ['name' => 'manage_backups', 'label' => 'Manage Backups', 'module' => 'Backup'],
        ];

        foreach ($permissions as $permission) {
            Permission::query()->updateOrCreate(
                ['name' => $permission['name']],
                [
                    'label' => $permission['label'],
                    'module' => $permission['module'],
                ]
            );
        }

        Permission::query()->whereIn('name', ['manage_purchases', 'manage_expenses'])->delete();
    }
}
