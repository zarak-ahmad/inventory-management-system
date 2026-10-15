<?php

namespace Database\Seeders;

use App\Models\Customer;
use Illuminate\Database\Seeder;

class CustomerSeeder extends Seeder
{
    public function run(): void
    {
        Customer::query()->updateOrCreate(
            ['is_default' => true],
            [
                'name' => Customer::WALK_IN_NAME,
                'phone' => null,
                'email' => null,
                'address' => null,
                'status' => Customer::STATUS_ACTIVE,
            ]
        );
    }
}
