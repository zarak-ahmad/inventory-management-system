<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            PermissionSeeder::class,
            RolePermissionSeeder::class,
            UnitSeeder::class,
            CustomerSeeder::class,
            SettingSeeder::class,
        ]);

        $superAdminRole = Role::query()->where('name', 'Super Admin')->firstOrFail();

        User::query()->updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Super Admin',
                'phone' => '0000000000',
                'password' => 'password',
                'role_id' => $superAdminRole->id,
                'status' => User::STATUS_ACTIVE,
                'email_verified_at' => now(),
            ]
        );
    }
}
