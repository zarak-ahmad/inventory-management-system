<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\Product;
use App\Models\Supplier;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class DashboardService
{
    /**
     * @return array<string, mixed>
     */
    public function metrics(): array
    {
        $productsCount = (int) Product::query()->count();
        $customersCount = (int) Customer::query()->count();
        $suppliersCount = (int) Supplier::query()->count();
        $lowStockCount = (int) Product::query()->lowStock()->count();
        $outOfStockCount = (int) Product::query()->where('current_stock', '<=', 0)->count();
        $inventoryValue = (float) Product::query()->sum(DB::raw('current_stock * purchase_price'));

        return [
            'products_count' => $productsCount,
            'customers_count' => $customersCount,
            'suppliers_count' => $suppliersCount,
            'low_stock_count' => $lowStockCount,
            'out_of_stock_count' => $outOfStockCount,
            'inventory_value' => $inventoryValue,
        ];
    }

    /**
     * Stock value grouped by category for chart.
     *
     * @return array{labels: list<string>, values: list<float>}
     */
    public function inventoryChart(): array
    {
        $rows = Product::query()
            ->leftJoin('categories', 'products.category_id', '=', 'categories.id')
            ->select(
                DB::raw("COALESCE(categories.name, 'Uncategorized') as category_name"),
                DB::raw('SUM(products.current_stock * products.purchase_price) as stock_value')
            )
            ->groupBy('category_name')
            ->orderByDesc('stock_value')
            ->limit(8)
            ->get();

        return [
            'labels' => $rows->pluck('category_name')->all(),
            'values' => $rows->map(fn ($row) => round((float) $row->stock_value, 2))->all(),
        ];
    }

    /**
     * @return Collection<int, Product>
     */
    public function lowStockProducts(int $limit = 8): Collection
    {
        return Product::query()
            ->with(['unit:id,short_name', 'category:id,name'])
            ->lowStock()
            ->orderBy('current_stock')
            ->limit($limit)
            ->get();
    }
}
