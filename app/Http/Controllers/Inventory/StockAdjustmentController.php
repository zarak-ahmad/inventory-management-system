<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Http\Requests\Inventory\StoreStockAdjustmentRequest;
use App\Models\Product;
use App\Models\StockAdjustment;
use App\Services\StockAdjustmentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Throwable;

class StockAdjustmentController extends Controller
{
    public function __construct(
        private readonly StockAdjustmentService $stockAdjustmentService
    ) {}

    public function index(Request $request): View
    {
        $search = trim((string) $request->query('search', ''));

        $adjustments = StockAdjustment::query()
            ->with(['product.unit', 'creator'])
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($inner) use ($search) {
                    $inner->where('reason', 'like', "%{$search}%")
                        ->orWhereHas('product', function ($productQuery) use ($search) {
                            $productQuery->where('name', 'like', "%{$search}%")
                                ->orWhere('sku', 'like', "%{$search}%");
                        });
                });
            })
            ->when($request->filled('adjustment_type'), fn ($q) => $q->where('adjustment_type', $request->query('adjustment_type')))
            ->when($request->filled('product_id'), fn ($q) => $q->where('product_id', $request->query('product_id')))
            ->when($request->filled('date_from'), fn ($q) => $q->whereDate('created_at', '>=', $request->query('date_from')))
            ->when($request->filled('date_to'), fn ($q) => $q->whereDate('created_at', '<=', $request->query('date_to')))
            ->latest('id')
            ->paginate(15)
            ->withQueryString();

        return view('stock-adjustments.index', [
            'adjustments' => $adjustments,
            'search' => $search,
            'types' => StockAdjustment::types(),
            'products' => Product::query()->orderBy('name')->get(['id', 'name', 'sku']),
        ]);
    }

    public function create(): View
    {
        return view('stock-adjustments.create', [
            'products' => Product::query()
                ->active()
                ->with('unit')
                ->orderBy('name')
                ->get(['id', 'name', 'sku', 'current_stock', 'unit_id']),
            'types' => StockAdjustment::types(),
        ]);
    }

    public function store(StoreStockAdjustmentRequest $request): RedirectResponse
    {
        try {
            $adjustment = $this->stockAdjustmentService->create(
                data: $request->validated(),
                userId: (int) $request->user()->id
            );
        } catch (Throwable $exception) {
            report($exception);

            return back()
                ->withInput()
                ->with('error', 'Unable to save stock adjustment. '.$exception->getMessage());
        }

        return redirect()
            ->route('stock-adjustments.show', $adjustment)
            ->with('success', 'Stock adjustment saved successfully.');
    }

    public function show(StockAdjustment $stockAdjustment): View
    {
        $stockAdjustment->load(['product.unit', 'creator']);

        return view('stock-adjustments.show', [
            'adjustment' => $stockAdjustment,
        ]);
    }
}
