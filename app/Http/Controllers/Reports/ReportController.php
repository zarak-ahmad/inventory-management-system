<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Services\ReportService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function __construct(
        private readonly ReportService $reportService
    ) {}

    public function index(): View
    {
        return view('reports.index');
    }

    public function sales(Request $request): View
    {
        $range = $this->reportService->dateRange($request);
        $data = $this->reportService->salesReport($range['date_from'], $range['date_to']);

        return view('reports.sales', [
            ...$range,
            ...$data,
            'periodLabel' => $this->reportService->periodLabel($range['date_from'], $range['date_to']),
        ]);
    }

    public function inventory(Request $request): View
    {
        $categoryId = $request->filled('category_id') ? (int) $request->query('category_id') : null;
        $data = $this->reportService->inventoryReport($categoryId);

        return view('reports.inventory', [
            ...$data,
            'categoryId' => $categoryId,
            'categories' => Category::query()->orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function lowStock(): View
    {
        $products = $this->reportService->lowStockReport();

        return view('reports.low-stock', [
            'products' => $products,
        ]);
    }

    public function customers(Request $request): View
    {
        $range = $this->reportService->dateRange($request);
        $customers = $this->reportService->customerReport($range['date_from'], $range['date_to']);

        return view('reports.customers', [
            ...$range,
            'customers' => $customers,
            'periodLabel' => $this->reportService->periodLabel($range['date_from'], $range['date_to']),
            'grandTotal' => (float) $customers->sum('sales_total'),
        ]);
    }
}
