<?php

namespace Database\Seeders;

use App\Models\Unit;
use Illuminate\Database\Seeder;

class UnitSeeder extends Seeder
{
    public function run(): void
    {
        $units = [
            ['name' => 'Piece', 'short_name' => 'pc'],
            ['name' => 'Box', 'short_name' => 'box'],
            ['name' => 'Bottle', 'short_name' => 'btl'],
            ['name' => 'Carton', 'short_name' => 'ctn'],
            ['name' => 'Kilogram', 'short_name' => 'kg'],
            ['name' => 'Gram', 'short_name' => 'g'],
            ['name' => 'Liter', 'short_name' => 'L'],
            ['name' => 'Meter', 'short_name' => 'm'],
        ];

        foreach ($units as $unit) {
            Unit::query()->updateOrCreate(
                ['name' => $unit['name']],
                ['short_name' => $unit['short_name']]
            );
        }
    }
}
