<?php

namespace Tests\Concerns;

use App\Models\Category;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Role;
use App\Models\Supplier;
use App\Models\Unit;
use App\Models\User;
use Database\Seeders\CustomerSeeder;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RolePermissionSeeder;
use Database\Seeders\RoleSeeder;
use Database\Seeders\SettingSeeder;
use Database\Seeders\UnitSeeder;

trait SeedsApplicationData
{
    protected function seedApplication(): void
    {
        $this->seed([
            RoleSeeder::class,
            PermissionSeeder::class,
            RolePermissionSeeder::class,
            UnitSeeder::class,
            CustomerSeeder::class,
            SettingSeeder::class,
        ]);
    }

    protected function makeAdmin(array $overrides = []): User
    {
        return User::factory()->create(array_merge([
            'email' => 'admin@test.local',
            'role_id' => Role::query()->where('name', 'Super Admin')->value('id'),
            'status' => User::STATUS_ACTIVE,
        ], $overrides));
    }

    protected function makeCashier(array $overrides = []): User
    {
        return User::factory()->create(array_merge([
            'email' => 'cashier@test.local',
            'role_id' => Role::query()->where('name', 'Cashier')->value('id'),
            'status' => User::STATUS_ACTIVE,
        ], $overrides));
    }

    protected function makeProduct(array $overrides = []): Product
    {
        $category = Category::query()->create([
            'name' => 'Test Category '.uniqid(),
            'status' => Category::STATUS_ACTIVE,
        ]);

        $supplier = Supplier::query()->create([
            'company_name' => 'Test Co',
            'supplier_name' => 'Test Supplier '.uniqid(),
            'phone' => '03001234567',
            'status' => Supplier::STATUS_ACTIVE,
        ]);

        $unit = Unit::query()->first() ?? Unit::query()->create([
            'name' => 'Piece',
            'short_name' => 'pc',
        ]);

        return Product::query()->create(array_merge([
            'category_id' => $category->id,
            'supplier_id' => $supplier->id,
            'unit_id' => $unit->id,
            'sku' => 'SKU-'.uniqid(),
            'barcode' => 'BC-'.uniqid(),
            'name' => 'Test Product '.uniqid(),
            'purchase_price' => 50,
            'selling_price' => 80,
            'tax' => 0,
            'discount' => 0,
            'current_stock' => 0,
            'minimum_stock' => 5,
            'status' => Product::STATUS_ACTIVE,
        ], $overrides));
    }

    protected function walkInCustomerId(): int
    {
        return (int) Customer::query()->where('is_default', true)->value('id');
    }
}
