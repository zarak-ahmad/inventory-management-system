<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\InventoryTransaction;
use App\Models\Product;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class InventoryController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('search', ''));

        $productsQuery = Product::query()
            ->with(['category', 'supplier', 'unit', 'brand'])
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($inner) use ($search) {
                    $inner->where('name', 'like', "%{$search}%")
                        ->orWhere('sku', 'like', "%{$search}%")
                        ->orWhere('barcode', 'like', "%{$search}%");
                });
            })
            ->when($request->filled('category_id'), fn ($q) => $q->where('category_id', $request->query('category_id')))
            ->when($request->filled('supplier_id'), fn ($q) => $q->where('supplier_id', $request->query('supplier_id')))
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->query('status')))
            ->when($request->boolean('low_stock'), fn ($q) => $q->lowStock())
            ->when($request->boolean('out_of_stock'), fn ($q) => $q->where('current_stock', '<=', 0));

        $summary = [
            'total_products' => (clone $productsQuery)->count(),
            'total_units' => (float) (clone $productsQuery)->sum('current_stock'),
            'stock_value' => (float) (clone $productsQuery)->sum(DB::raw('current_stock * purchase_price')),
            'low_stock' => (clone $productsQuery)->lowStock()->count(),
            'out_of_stock' => (clone $productsQuery)->where('current_stock', '<=', 0)->count(),
        ];

        $products = $productsQuery
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return view('inventory.index', [
            'products' => $products,
            'summary' => $summary,
            'search' => $search,
            'categories' => Category::query()->orderBy('name')->get(),
            'suppliers' => Supplier::query()->orderBy('supplier_name')->get(),
        ]);
    }

    public function transactions(Request $request): View
    {
        $search = trim((string) $request->query('search', ''));

        $transactions = InventoryTransaction::query()
            ->with(['product.unit', 'creator'])
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($inner) use ($search) {
                    $inner->where('remarks', 'like', "%{$search}%")
                        ->orWhereHas('product', function ($productQuery) use ($search) {
                            $productQuery->where('name', 'like', "%{$search}%")
                                ->orWhere('sku', 'like', "%{$search}%");
                        });
                });
            })
            ->when($request->filled('product_id'), fn ($q) => $q->where('product_id', $request->query('product_id')))
            ->when($request->filled('transaction_type'), fn ($q) => $q->where('transaction_type', $request->query('transaction_type')))
            ->when($request->filled('date_from'), fn ($q) => $q->whereDate('created_at', '>=', $request->query('date_from')))
            ->when($request->filled('date_to'), fn ($q) => $q->whereDate('created_at', '<=', $request->query('date_to')))
            ->latest('id')
            ->paginate(20)
            ->withQueryString();

        return view('inventory.transactions', [
            'transactions' => $transactions,
            'search' => $search,
            'products' => Product::query()->orderBy('name')->get(['id', 'name', 'sku']),
            'types' => [
                InventoryTransaction::TYPE_PURCHASE,
                InventoryTransaction::TYPE_SALE,
                InventoryTransaction::TYPE_ADJUSTMENT,
                InventoryTransaction::TYPE_MANUAL,
            ],
        ]);
    }

    public function show(Product $product): View
    {
        $product->load(['category', 'brand', 'supplier', 'unit']);

        $transactions = InventoryTransaction::query()
            ->with('creator')
            ->where('product_id', $product->id)
            ->latest('id')
            ->paginate(20);

        $stockValue = (float) $product->current_stock * (float) $product->purchase_price;

        return view('inventory.show', [
            'product' => $product,
            'transactions' => $transactions,
            'stockValue' => $stockValue,
        ]);
    }

    public function lowStock(Request $request): View
    {
        $search = trim((string) $request->query('search', ''));

        $products = Product::query()
            ->with(['category', 'supplier', 'unit'])
            ->lowStock()
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($inner) use ($search) {
                    $inner->where('name', 'like', "%{$search}%")
                        ->orWhere('sku', 'like', "%{$search}%");
                });
            })
            ->orderBy('current_stock')
            ->paginate(15)
            ->withQueryString();

        return view('inventory.low-stock', [
            'products' => $products,
            'search' => $search,
        ]);
    }
}
