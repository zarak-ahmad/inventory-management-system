<?php

namespace App\Http\Controllers\Sales;

use App\Http\Controllers\Controller;
use App\Http\Requests\Sales\StoreSaleRequest;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Sale;
use App\Services\SaleService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Throwable;

class SaleController extends Controller
{
    public function __construct(
        private readonly SaleService $saleService
    ) {}

    public function index(Request $request): View
    {
        $search = trim((string) $request->query('search', ''));

        $sales = Sale::query()
            ->with(['customer', 'user'])
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($inner) use ($search) {
                    $inner->where('invoice_number', 'like', "%{$search}%")
                        ->orWhereHas('customer', fn ($q) => $q->where('name', 'like', "%{$search}%"));
                });
            })
            ->when($request->filled('payment_method'), fn ($q) => $q->where('payment_method', $request->query('payment_method')))
            ->when($request->filled('user_id'), fn ($q) => $q->where('user_id', $request->query('user_id')))
            ->when($request->filled('date_from'), fn ($q) => $q->whereDate('created_at', '>=', $request->query('date_from')))
            ->when($request->filled('date_to'), fn ($q) => $q->whereDate('created_at', '<=', $request->query('date_to')))
            ->latest('id')
            ->paginate(15)
            ->withQueryString();

        return view('sales.index', [
            'sales' => $sales,
            'search' => $search,
            'paymentMethods' => Sale::paymentMethods(),
        ]);
    }

    public function pos(Request $request): View
    {
        $products = Product::query()
            ->active()
            ->with(['category', 'unit'])
            ->where('current_stock', '>', 0)
            ->orderBy('name')
            ->get();

        $addProductId = $request->filled('product_id') ? (int) $request->query('product_id') : null;

        if ($addProductId && ! $products->contains('id', $addProductId)) {
            $extra = Product::query()
                ->active()
                ->with(['category', 'unit'])
                ->find($addProductId);

            if ($extra) {
                $products = $products->prepend($extra)->values();
            }
        }

        $customers = Customer::query()
            ->active()
            ->orderByDesc('is_default')
            ->orderBy('name')
            ->get(['id', 'name', 'phone', 'is_default']);

        $walkInId = $customers->firstWhere('is_default', true)?->id;

        return view('pos.index', [
            'products' => $products,
            'categories' => Category::query()->active()->orderBy('name')->get(),
            'customers' => $customers,
            'walkInId' => $walkInId,
            'paymentMethods' => Sale::paymentMethods(),
            'addProductId' => $addProductId,
        ]);
    }

    public function store(StoreSaleRequest $request): RedirectResponse
    {
        try {
            $sale = $this->saleService->create(
                data: $request->safe()->except('items'),
                items: $request->validated('items'),
                userId: (int) $request->user()->id
            );
        } catch (Throwable $exception) {
            report($exception);

            return back()
                ->withInput()
                ->with('error', 'Unable to complete sale. '.$exception->getMessage());
        }

        return redirect()
            ->route('sales.show', $sale)
            ->with('success', 'Sale completed successfully.');
    }

    public function show(Sale $sale): View
    {
        $sale->load(['customer', 'user', 'items.product.unit']);

        return view('sales.show', [
            'sale' => $sale,
        ]);
    }
}
