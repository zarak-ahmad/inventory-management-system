<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Services\DashboardService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(
        private readonly DashboardService $dashboardService
    ) {}

    public function index(): View
    {
        return view('dashboard.index', [
            'metrics' => $this->dashboardService->metrics(),
            'inventoryChart' => $this->dashboardService->inventoryChart(),
            'lowStockProducts' => $this->dashboardService->lowStockProducts(),
        ]);
    }

    public function searchProducts(Request $request): JsonResponse
    {
        $query = trim((string) $request->query('q', ''));

        if (mb_strlen($query) < 3) {
            return response()->json(['products' => []]);
        }

        $user = $request->user();
        $canViewProducts = $user?->hasPermission('manage_products') ?? false;
        $canViewInventory = $user?->hasPermission('manage_inventory') ?? false;

        $products = Product::query()
            ->with(['unit:id,short_name', 'category:id,name'])
            ->where(function ($builder) use ($query) {
                $builder->where('name', 'like', "%{$query}%")
                    ->orWhere('barcode', 'like', "%{$query}%")
                    ->orWhere('sku', 'like', "%{$query}%");
            })
            ->orderBy('name')
            ->limit(10)
            ->get(['id', 'name', 'sku', 'barcode', 'location', 'current_stock', 'selling_price', 'unit_id', 'category_id', 'status']);

        return response()->json([
            'products' => $products->map(function (Product $product) use ($canViewProducts, $canViewInventory) {
                $url = null;
                if ($canViewProducts) {
                    $url = route('products.show', $product);
                } elseif ($canViewInventory) {
                    $url = route('inventory.show', $product);
                }

                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'sku' => $product->sku,
                    'barcode' => $product->barcode,
                    'location' => $product->location,
                    'stock' => (float) $product->current_stock,
                    'unit' => $product->unit?->short_name,
                    'category' => $product->category?->name,
                    'selling_price' => (float) $product->selling_price,
                    'status' => $product->status,
                    'url' => $url,
                ];
            })->values(),
        ]);
    }
}
