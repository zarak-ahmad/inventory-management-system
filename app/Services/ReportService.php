<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\Expense;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Supplier;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ReportService
{
    /**
     * @return array{date_from:string, date_to:string}
     */
    public function dateRange(Request $request, ?string $defaultFrom = null, ?string $defaultTo = null): array
    {
        return [
            'date_from' => $request->query('date_from', $defaultFrom ?? now()->startOfMonth()->toDateString()),
            'date_to' => $request->query('date_to', $defaultTo ?? now()->toDateString()),
        ];
    }

    /**
     * @return array{sales:Collection, daily:Collection, monthly:Collection, summary:array<string, float|int>}
     */
    public function salesReport(string $dateFrom, string $dateTo): array
    {
        $sales = Sale::query()
            ->with(['customer:id,name', 'user:id,name'])
            ->whereDate('created_at', '>=', $dateFrom)
            ->whereDate('created_at', '<=', $dateTo)
            ->latest('id')
            ->get();

        $daily = Sale::query()
            ->select(
                DB::raw('DATE(created_at) as period'),
                DB::raw('COUNT(*) as count'),
                DB::raw('SUM(subtotal) as subtotal'),
                DB::raw('SUM(discount) as discount'),
                DB::raw('SUM(tax) as tax'),
                DB::raw('SUM(total) as total')
            )
            ->whereDate('created_at', '>=', $dateFrom)
            ->whereDate('created_at', '<=', $dateTo)
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy('period')
            ->get();

        $monthly = Sale::query()
            ->select(
                DB::raw("strftime('%Y-%m', created_at) as period"),
                DB::raw('COUNT(*) as count'),
                DB::raw('SUM(total) as total')
            )
            ->whereDate('created_at', '>=', $dateFrom)
            ->whereDate('created_at', '<=', $dateTo)
            ->groupBy(DB::raw("strftime('%Y-%m', created_at)"))
            ->orderBy('period')
            ->get();

        return [
            'sales' => $sales,
            'daily' => $daily,
            'monthly' => $monthly,
            'summary' => [
                'count' => $sales->count(),
                'subtotal' => (float) $sales->sum('subtotal'),
                'discount' => (float) $sales->sum('discount'),
                'tax' => (float) $sales->sum('tax'),
                'total' => (float) $sales->sum('total'),
            ],
        ];
    }

    /**
     * @return array{purchases:Collection, by_supplier:Collection, summary:array<string, float|int>}
     */
    public function purchaseReport(string $dateFrom, string $dateTo, ?int $supplierId = null): array
    {
        $query = Purchase::query()
            ->with(['supplier:id,supplier_name', 'user:id,name'])
            ->whereDate('purchase_date', '>=', $dateFrom)
            ->whereDate('purchase_date', '<=', $dateTo)
            ->when($supplierId, fn ($q) => $q->where('supplier_id', $supplierId));

        $purchases = (clone $query)->latest('purchase_date')->latest('id')->get();

        $bySupplier = (clone $query)
            ->select('supplier_id', DB::raw('COUNT(*) as count'), DB::raw('SUM(total) as total'))
            ->groupBy('supplier_id')
            ->with('supplier:id,supplier_name')
            ->orderByDesc('total')
            ->get();

        return [
            'purchases' => $purchases,
            'by_supplier' => $bySupplier,
            'summary' => [
                'count' => $purchases->count(),
                'subtotal' => (float) $purchases->sum('subtotal'),
                'discount' => (float) $purchases->sum('discount'),
                'tax' => (float) $purchases->sum('tax'),
                'total' => (float) $purchases->sum('total'),
            ],
        ];
    }

    /**
     * @return array{products:Collection, summary:array<string, float|int>}
     */
    public function inventoryReport(?int $categoryId = null): array
    {
        $query = Product::query()
            ->with(['category:id,name', 'unit:id,short_name', 'supplier:id,supplier_name'])
            ->when($categoryId, fn ($q) => $q->where('category_id', $categoryId))
            ->orderBy('name');

        $products = $query->get();

        return [
            'products' => $products,
            'summary' => [
                'count' => $products->count(),
                'total_units' => (float) $products->sum('current_stock'),
                'stock_value' => (float) $products->sum(fn ($p) => (float) $p->current_stock * (float) $p->purchase_price),
                'retail_value' => (float) $products->sum(fn ($p) => (float) $p->current_stock * (float) $p->selling_price),
                'low_stock' => $products->filter(fn ($p) => $p->isLowStock())->count(),
            ],
        ];
    }

    /**
     * @return Collection<int, Product>
     */
    public function lowStockReport(): Collection
    {
        return Product::query()
            ->with(['category:id,name', 'unit:id,short_name', 'supplier:id,supplier_name'])
            ->lowStock()
            ->orderBy('current_stock')
            ->orderBy('name')
            ->get();
    }

    /**
     * @return array{expenses:Collection, by_category:Collection, summary:array<string, float|int>}
     */
    public function expenseReport(string $dateFrom, string $dateTo, ?string $category = null): array
    {
        $query = Expense::query()
            ->with('creator:id,name')
            ->whereDate('expense_date', '>=', $dateFrom)
            ->whereDate('expense_date', '<=', $dateTo)
            ->when($category, fn ($q) => $q->where('category', $category));

        $expenses = (clone $query)->latest('expense_date')->latest('id')->get();

        $byCategory = (clone $query)
            ->select('category', DB::raw('COUNT(*) as count'), DB::raw('SUM(amount) as total'))
            ->groupBy('category')
            ->orderByDesc('total')
            ->get();

        return [
            'expenses' => $expenses,
            'by_category' => $byCategory,
            'summary' => [
                'count' => $expenses->count(),
                'total' => (float) $expenses->sum('amount'),
            ],
        ];
    }

    /**
     * @return array<string, float|int|string>
     */
    public function profitAndLoss(string $dateFrom, string $dateTo): array
    {
        $salesTotal = (float) Sale::query()
            ->whereDate('created_at', '>=', $dateFrom)
            ->whereDate('created_at', '<=', $dateTo)
            ->sum('total');

        $salesCount = (int) Sale::query()
            ->whereDate('created_at', '>=', $dateFrom)
            ->whereDate('created_at', '<=', $dateTo)
            ->count();

        $cogs = (float) SaleItem::query()
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->join('products', 'sale_items.product_id', '=', 'products.id')
            ->whereDate('sales.created_at', '>=', $dateFrom)
            ->whereDate('sales.created_at', '<=', $dateTo)
            ->sum(DB::raw('sale_items.quantity * products.purchase_price'));

        $expensesTotal = (float) Expense::query()
            ->whereDate('expense_date', '>=', $dateFrom)
            ->whereDate('expense_date', '<=', $dateTo)
            ->sum('amount');

        $purchasesTotal = (float) Purchase::query()
            ->whereDate('purchase_date', '>=', $dateFrom)
            ->whereDate('purchase_date', '<=', $dateTo)
            ->sum('total');

        $grossProfit = $salesTotal - $cogs;
        $netProfit = $grossProfit - $expensesTotal;

        return [
            'date_from' => $dateFrom,
            'date_to' => $dateTo,
            'sales_count' => $salesCount,
            'sales_total' => $salesTotal,
            'cogs' => $cogs,
            'gross_profit' => $grossProfit,
            'expenses_total' => $expensesTotal,
            'net_profit' => $netProfit,
            'purchases_total' => $purchasesTotal,
        ];
    }

    /**
     * @return Collection<int, object>
     */
    public function customerReport(string $dateFrom, string $dateTo): Collection
    {
        return Customer::query()
            ->whereHas('sales', function ($query) use ($dateFrom, $dateTo) {
                $query->whereDate('created_at', '>=', $dateFrom)
                    ->whereDate('created_at', '<=', $dateTo);
            })
            ->withCount(['sales as sales_count' => function ($query) use ($dateFrom, $dateTo) {
                $query->whereDate('created_at', '>=', $dateFrom)
                    ->whereDate('created_at', '<=', $dateTo);
            }])
            ->withSum(['sales as sales_total' => function ($query) use ($dateFrom, $dateTo) {
                $query->whereDate('created_at', '>=', $dateFrom)
                    ->whereDate('created_at', '<=', $dateTo);
            }], 'total')
            ->orderByDesc('sales_total')
            ->get();
    }

    /**
     * @return Collection<int, object>
     */
    public function supplierReport(string $dateFrom, string $dateTo): Collection
    {
        return Supplier::query()
            ->whereHas('purchases', function ($query) use ($dateFrom, $dateTo) {
                $query->whereDate('purchase_date', '>=', $dateFrom)
                    ->whereDate('purchase_date', '<=', $dateTo);
            })
            ->withCount(['purchases as purchases_count' => function ($query) use ($dateFrom, $dateTo) {
                $query->whereDate('purchase_date', '>=', $dateFrom)
                    ->whereDate('purchase_date', '<=', $dateTo);
            }])
            ->withSum(['purchases as purchases_total' => function ($query) use ($dateFrom, $dateTo) {
                $query->whereDate('purchase_date', '>=', $dateFrom)
                    ->whereDate('purchase_date', '<=', $dateTo);
            }], 'total')
            ->orderByDesc('purchases_total')
            ->get();
    }

    public function periodLabel(string $dateFrom, string $dateTo): string
    {
        return Carbon::parse($dateFrom)->format('Y-m-d').' → '.Carbon::parse($dateTo)->format('Y-m-d');
    }
}
